<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function checkout(Request $request, Appointment $appointment)
    {
        try {
            if (!$appointment->price || !$appointment->price->stripe_price_id) {
                throw new \Exception('La cita no tiene un precio válido configurado');
            }

            Log::info('Iniciando checkout con:', [
                'stripe_price_id' => $appointment->price->stripe_price_id,
                'appointment_id' => $appointment->id
            ]);

            $checkout = $request->user()->checkout([
                [
                    'price' => $appointment->price->stripe_price_id,
                    'quantity' => 1,
                ]
            ], [
                'success_url' => route('payment.success', [
                    'appointment' => $appointment->id
                ]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', $appointment->id),
                'metadata' => [
                    'appointment_id' => $appointment->id,
                    'user_id' => $request->user()->id,
                ],
                'client_reference_id' => (string) $appointment->id,
                'mode' => 'payment',
                'payment_method_types' => ['card'],
            ]);

            return $checkout;
        } catch (\Exception $e) {
            Log::error('Error en checkout:', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id
            ]);
            return back()->withErrors(['error' => 'Error al procesar el pago: ' . $e->getMessage()]);
        }
    }

    public function success(Request $request, Appointment $appointment)
    {
        try {
            $sessionId = $request->query('session_id');

            Log::info('Procesando éxito de pago', [
                'appointment_id' => $appointment->id,
                'session_id' => $sessionId,
                'current_payment_status' => $appointment->payment_status,
            ]);

            if (!$sessionId) {
                throw new \Exception('No se proporcionó el ID de sesión');
            }

            $session = $request->user()->stripe()->checkout->sessions->retrieve($sessionId);

            Log::info('Detalles de la sesión de Stripe', [
                'session_id' => $session->id,
                'payment_status' => $session->payment_status,
                'client_reference_id' => $session->client_reference_id,
            ]);

            // Verificar que la sesión corresponde a esta cita
            if ($session->client_reference_id != $appointment->id) {
                throw new \Exception('La sesión de pago no corresponde a esta cita');
            }

            if ($session->payment_status === 'paid') {
                // Forzar una recarga del modelo antes de la actualización
                $appointment = $appointment->fresh();

                // Guardar el estado anterior para logging
                $previousStatus = $appointment->payment_status;

                // Forzar la actualización de todos los campos relevantes
                $appointment->payment_status = 'paid';
                $appointment->stripe_payment_intent = $session->payment_intent;
                $appointment->stripe_checkout_session = $session->id;

                // Intentar guardar y capturar cualquier error
                try {
                    $saved = $appointment->save();

                    // Verificar el estado después de guardar
                    $appointment = $appointment->fresh();

                    Log::info('Actualización del estado de pago', [
                        'appointment_id' => $appointment->id,
                        'previous_status' => $previousStatus,
                        'new_status' => $appointment->payment_status,
                        'save_success' => $saved,
                        'current_db_status' => $appointment->payment_status,
                    ]);

                    if ($appointment->payment_status !== 'paid') {
                        throw new \Exception('El estado de pago no se actualizó correctamente en la base de datos');
                    }
                } catch (\Exception $e) {
                    Log::error('Error al guardar el estado de pago', [
                        'error' => $e->getMessage(),
                        'appointment_id' => $appointment->id,
                    ]);
                    throw $e;
                }

                return redirect()->route('filament.admin.resources.appointments.index')
                    ->with('success', 'Pago realizado con éxito');
            } else {
                Log::warning('El pago no está marcado como pagado', [
                    'payment_status' => $session->payment_status,
                ]);
                throw new \Exception('El pago no está en estado "paid"');
            }
        } catch (ApiErrorException $e) {
            Log::error('Error de Stripe en success', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
            ]);
            return redirect()->route('filament.admin.resources.appointments.index')
                ->withErrors(['error' => 'Error al verificar el pago: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Error general en success', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id,
            ]);
            return redirect()->route('filament.admin.resources.appointments.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->update([
            'payment_status' => 'cancelled'
        ]);

        return redirect()->route('filament.admin.resources.appointments.index')
            ->with('warning', 'El pago ha sido cancelado');
    }
}
