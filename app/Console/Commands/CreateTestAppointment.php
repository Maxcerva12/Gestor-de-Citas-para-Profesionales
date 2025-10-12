<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;

class CreateTestAppointment extends Command
{
    protected $signature = 'test:create-appointment';
    protected $description = 'Crear una cita de prueba para verificar la sincronización automática';

    public function handle()
    {
        // Obtener datos necesarios
        $client = Client::first();
        $schedule = Schedule::where('is_available', true)->first();
        $service = Service::first();

        if (!$client) {
            $this->error('No hay clientes en la base de datos');
            return 1;
        }

        if (!$schedule) {
            $this->error('No hay horarios disponibles');
            return 1;
        }

        $this->info("Creando cita de prueba...");
        $this->info("Cliente: {$client->name}");
        $this->info("Profesional: {$schedule->user->name}");
        $this->info("Fecha/Hora: {$schedule->date} {$schedule->start_time}");

        // Crear fechas/horas más simples
        $startTime = now()->addDays(2)->setHour(10)->setMinute(0)->setSecond(0);
        $endTime = $startTime->copy()->addHour();

        // Crear la cita (esto debería disparar el Observer automáticamente)
        $appointment = Appointment::create([
            'user_id' => $schedule->user_id,
            'client_id' => $client->id,
            'schedule_id' => $schedule->id,
            'service_id' => $service ? $service->id : null,
            'service_price' => $service ? $service->price : 50000,
            'payment_method' => 'efectivo',
            'payment_status' => 'pending',
            'start_time' => $startTime,
            'end_time' => $endTime,
            'notes' => 'Cita de prueba creada desde comando',
            'status' => 'pending',
        ]);

        $this->info("✓ Cita creada con ID: {$appointment->id}");

        // Esperar un momento para que el Observer procese
        sleep(2);

        // Verificar si se sincronizó
        $appointment->refresh();

        if ($appointment->google_event_id) {
            $this->info("✓ Cita sincronizada automáticamente con Google Calendar");
            $this->info("ID del evento: {$appointment->google_event_id}");
        } else {
            $this->error("✗ La cita NO se sincronizó automáticamente");
            $this->info("Revisa los logs en storage/logs/laravel.log");
        }

        return 0;
    }
}