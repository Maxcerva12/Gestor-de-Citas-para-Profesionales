<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Enums\InvoiceType;
use App\Enums\InvoiceState;

class AppointmentObserver
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        // Verificar si el profesional (user) tiene configurado Google Calendar
        if ($appointment->user && $appointment->user->google_token) {
            try {
                // Temporalmente establecer el usuario profesional como autenticado para la sincronización
                $originalUser = Auth::user();
                Auth::login($appointment->user);

                $eventId = $this->googleService->createEvent($appointment);
                $appointment->google_event_id = $eventId;
                $appointment->save();

                // Restaurar el usuario original
                if ($originalUser) {
                    Auth::login($originalUser);
                } else {
                    Auth::logout();
                }
            } catch (\Exception $e) {
                // Log del error para debugging
                \Log::error('Error al sincronizar cita con Google Calendar: ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'user_id' => $appointment->user_id,
                    'error' => $e->getTraceAsString()
                ]);
            }
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // Verificar si cambió el estado de pago
        if ($appointment->isDirty('payment_status')) {
            $this->handlePaymentStatusChange($appointment);
        }

        // Actualizar evento de Google Calendar si es necesario
        if ($appointment->google_event_id && $appointment->user && $appointment->user->google_token) {
            $changedFields = ['start_time', 'end_time', 'status', 'notes'];
            if ($appointment->isDirty($changedFields)) {
                try {
                    $originalUser = Auth::user();
                    Auth::login($appointment->user);

                    // TODO: Implementar updateEvent en GoogleCalendarService
                    // $this->googleService->updateEvent($appointment);

                    if ($originalUser) {
                        Auth::login($originalUser);
                    } else {
                        Auth::logout();
                    }
                } catch (\Exception $e) {
                    \Log::error('Error al actualizar evento en Google Calendar: ' . $e->getMessage(), [
                        'appointment_id' => $appointment->id,
                        'google_event_id' => $appointment->google_event_id,
                    ]);
                }
            }
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Manejar cambios en el estado de pago y generar facturas automáticamente
     */
    private function handlePaymentStatusChange(Appointment $appointment): void
    {
        $newStatus = $appointment->payment_status;
        $oldStatus = $appointment->getOriginal('payment_status');

        // Solo procesar si realmente cambió el estado
        if ($newStatus === $oldStatus) {
            return;
        }

        try {
            // Cuando el pago cambia a 'paid' o 'failed', generar factura automáticamente
            if (in_array($newStatus, ['paid', 'failed']) && !$appointment->invoices()->exists()) {
                $this->createInvoiceFromAppointment($appointment);
            }
        } catch (\Exception $e) {
            \Log::error('Error al generar factura automática: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'payment_status' => $newStatus,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Crear factura automáticamente desde una cita
     */
    private function createInvoiceFromAppointment(Appointment $appointment): void
    {
        // Verificar que la cita tenga los datos necesarios
        if (!$appointment->client || !$appointment->service_price) {
            \Log::warning('No se puede crear factura automática: faltan datos', [
                'appointment_id' => $appointment->id,
                'has_client' => (bool) $appointment->client,
                'has_service_price' => (bool) $appointment->service_price
            ]);
            return;
        }

        // Crear la factura usando el método correcto del modelo base
        $invoice = new Invoice();
        $invoice->appointment_id = $appointment->id;
        $invoice->user_id = $appointment->user_id;
        $invoice->buyer_type = 'App\Models\Client';
        $invoice->buyer_id = $appointment->client_id;
        $invoice->type = InvoiceType::Invoice;
        $invoice->state = $appointment->payment_status === 'paid' ? InvoiceState::Paid : InvoiceState::Draft;
        $invoice->currency = 'COP';
        $invoice->due_at = now()->addDays(30);
        $invoice->description = 'Factura por servicio: ' . ($appointment->service ? $appointment->service->name : 'Servicio Profesional');
        $invoice->save();

        // Crear el ítem de la factura basado en el servicio
        $invoiceItem = new InvoiceItem();
        $invoiceItem->invoice_id = $invoice->id;
        $invoiceItem->label = $appointment->service ? $appointment->service->name : 'Servicio Profesional';
        $invoiceItem->description = $appointment->service ? $appointment->service->description : $appointment->notes;
        $invoiceItem->quantity = 1;
        $invoiceItem->unit_price = (int) $appointment->service_price; // El sistema ya maneja los centavos internamente
        $invoiceItem->currency = 'COP';
        // $invoiceItem->tax_percentage = null; // Sin IVA por defecto para servicios profesionales
        $invoiceItem->save();

        // Denormalizar la factura para calcular totales
        $invoice->denormalize();

        \Log::info('Factura creada automáticamente', [
            'appointment_id' => $appointment->id,
            'invoice_id' => $invoice->id,
            'payment_status' => $appointment->payment_status
        ]);
    }
}
