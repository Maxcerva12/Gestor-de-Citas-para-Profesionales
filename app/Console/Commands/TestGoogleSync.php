<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestGoogleSync extends Command
{
    protected $signature = 'test:google-sync {appointment_id}';
    protected $description = 'Probar sincronización con Google Calendar para una cita específica';

    public function handle()
    {
        $appointmentId = $this->argument('appointment_id');

        $appointment = Appointment::with(['user', 'client'])->find($appointmentId);

        if (!$appointment) {
            $this->error("No se encontró la cita con ID: {$appointmentId}");
            return 1;
        }

        $this->info("Probando sincronización para cita ID: {$appointmentId}");
        $this->info("Cliente: {$appointment->client->name}");
        $this->info("Profesional: {$appointment->user->name}");
        $this->info("Fecha: {$appointment->start_time}");

        // Verificar si el profesional tiene token de Google
        if (!$appointment->user->google_token) {
            $this->error("El profesional no tiene configurado Google Calendar");
            $this->info("Para configurar Google Calendar:");
            $this->info("1. Accede al panel de administración como el profesional");
            $this->info("2. Ve a la sección de Citas");
            $this->info("3. Haz clic en 'Conectar Google Calendar'");
            return 1;
        }

        $this->info("✓ El profesional tiene Google Calendar configurado");

        try {
            // Simular la lógica del Observer
            $originalUser = Auth::user();
            Auth::login($appointment->user);

            $googleService = app(GoogleCalendarService::class);
            $eventId = $googleService->createEvent($appointment);

            $appointment->google_event_id = $eventId;
            $appointment->save();

            if ($originalUser) {
                Auth::login($originalUser);
            } else {
                Auth::logout();
            }

            $this->info("✓ Cita sincronizada exitosamente con Google Calendar");
            $this->info("ID del evento: {$eventId}");

        } catch (\Exception $e) {
            $this->error("Error al sincronizar: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}