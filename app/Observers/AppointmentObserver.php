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
        Log::info("AppointmentObserver: Cita creada {$appointment->id} para cliente {$appointment->client_id}");

        // Marcar el horario como no disponible cuando se crea una cita
        if ($appointment->schedule) {
            try {
                $appointment->schedule->update(['is_available' => false]);
                Log::info("Horario {$appointment->schedule->id} marcado como no disponible por cita {$appointment->id}");
            } catch (\Exception $e) {
                Log::error("Error al marcar horario como no disponible para cita {$appointment->id}: " . $e->getMessage());
            }
        }

        // Enviar notificación al profesional sobre la nueva cita
        if ($appointment->user) {
            try {
                $appointment->load('client', 'user');
                $appointment->user->notify(new \App\Notifications\AppointmentCreatedForProfessional($appointment));
                Log::info("AppointmentObserver: Notificación enviada al profesional {$appointment->user->name} (ID: {$appointment->user->id}) por cita {$appointment->id}");
            } catch (\Exception $e) {
                Log::error("AppointmentObserver: Error al enviar notificación al profesional para cita {$appointment->id}: " . $e->getMessage());
            }
        } else {
            Log::warning("AppointmentObserver: Cita {$appointment->id} sin profesional asociado para enviar notificación");
        }

        // Verificar configuración de Google Calendar
        if (!$appointment->user) {
            Log::warning("AppointmentObserver: Cita {$appointment->id} sin profesional asociado");
            return;
        }

        if (!$appointment->user->google_token) {
            Log::info("AppointmentObserver: Profesional {$appointment->user->name} sin Google Calendar configurado");
            return;
        }

        // Sincronizar con Google Calendar
        Log::info("AppointmentObserver: Iniciando sincronización con Google Calendar para cita {$appointment->id}");

        try {
            // Crear una instancia nueva del servicio para evitar conflictos de contexto
            $googleService = app(\App\Services\GoogleCalendarService::class);

            // Establecer directamente el token sin cambiar la autenticación de sesión
            $googleService->getClient()->setAccessToken($appointment->user->google_token);

            $eventId = $googleService->createEvent($appointment);

            // Usar updateQuietly para evitar disparar eventos del Observer nuevamente
            $appointment->updateQuietly(['google_event_id' => $eventId]);

            Log::info("AppointmentObserver: Cita {$appointment->id} sincronizada exitosamente con Google Calendar (Event ID: {$eventId})");

        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('AppointmentObserver: Error al sincronizar cita con Google Calendar', [
                'appointment_id' => $appointment->id,
                'user_id' => $appointment->user_id,
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile()
            ]);
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // Si la cita fue cancelada, hacer el horario disponible nuevamente
        // solo si no ha expirado
        if ($appointment->isDirty('status') && $appointment->status === 'canceled') {
            Log::info("Observer: Cita {$appointment->id} cancelada");

            // Enviar notificación según quién canceló
            try {
                $appointment->load('client', 'user');
                
                if ($appointment->cancelled_by === 'client') {
                    // Notificar al profesional
                    if ($appointment->user) {
                        $appointment->user->notify(new \App\Notifications\AppointmentCancelledByClient($appointment));
                        Log::info("Observer: Notificación de cancelación enviada al profesional {$appointment->user->name} (ID: {$appointment->user->id})");
                    }
                } elseif ($appointment->cancelled_by === 'professional') {
                    // Notificar al cliente
                    if ($appointment->client) {
                        $appointment->client->notify(new \App\Notifications\AppointmentCancelledByProfessional($appointment));
                        Log::info("Observer: Notificación de cancelación enviada al cliente {$appointment->client->name} (ID: {$appointment->client->id})");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Observer: Error al enviar notificación de cancelación para cita {$appointment->id}: " . $e->getMessage());
            }

            if ($appointment->schedule) {
                try {
                    Log::info("Observer: Procesando horario {$appointment->schedule->id} para cita cancelada {$appointment->id}");

                    // Verificar si el horario ha expirado  
                    $date = \Carbon\Carbon::parse($appointment->schedule->date)->format('Y-m-d');
                    $scheduleDateTime = \Carbon\Carbon::parse($date . ' ' . $appointment->schedule->start_time);
                    Log::info("Observer: Fecha/hora del horario: {$scheduleDateTime}, Ahora: " . \Carbon\Carbon::now());
                    if (!$scheduleDateTime->isPast()) {
                        $appointment->schedule->update(['is_available' => true]);
                        Log::info("Observer: Horario {$appointment->schedule->id} marcado como disponible por cancelación de cita {$appointment->id}");
                    } else {
                        Log::info("Observer: Horario {$appointment->schedule->id} ha expirado, no se marca como disponible");
                    }
                } catch (\Exception $e) {
                    Log::error("Observer: Error al procesar cancelación de cita {$appointment->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                }
            } else {
                Log::warning("Observer: Cita {$appointment->id} no tiene horario asociado");
            }
        }

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
        
        $this->invalidateDashboardCache($appointment);
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        Log::info("Observer: Eliminando cita {$appointment->id}");
        $this->invalidateDashboardCache($appointment);

        // Si se elimina la cita, hacer el horario disponible nuevamente
        // solo si no ha expirado
        if ($appointment->schedule) {
            try {
                Log::info("Observer: Procesando horario {$appointment->schedule->id} para cita eliminada {$appointment->id}");

                // Verificar si el horario ha expirado
                $date = \Carbon\Carbon::parse($appointment->schedule->date)->format('Y-m-d');
                $scheduleDateTime = \Carbon\Carbon::parse($date . ' ' . $appointment->schedule->start_time);
                Log::info("Observer: Fecha/hora del horario: {$scheduleDateTime}, Ahora: " . \Carbon\Carbon::now());

                if (!$scheduleDateTime->isPast()) {
                    $appointment->schedule->update(['is_available' => true]);
                    Log::info("Observer: Horario {$appointment->schedule->id} marcado como disponible por eliminación de cita {$appointment->id}");
                } else {
                    Log::info("Observer: Horario {$appointment->schedule->id} ha expirado, no se marca como disponible");
                }
            } catch (\Exception $e) {
                Log::error("Observer: Error al procesar eliminación de cita {$appointment->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            }
        } else {
            Log::warning("Observer: Cita {$appointment->id} no tiene horario asociado");
        }
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
        
        $this->invalidateDashboardCache($appointment);
    }

    /**
     * Invalidar caché del dashboard cuando cambian las citas
     */
    protected function invalidateDashboardCache(Appointment $appointment): void
    {
        $service = app(\App\Services\DashboardDataService::class);
        $service->invalidateCache($appointment->user_id);
    }
}
