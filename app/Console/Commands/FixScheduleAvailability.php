<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FixScheduleAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedules:fix-availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige la disponibilidad de horarios que no tienen citas activas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando corrección de disponibilidad de horarios...');

        try {
            // Obtener horarios que están marcados como no disponibles
            $unavailableSchedules = Schedule::where('is_available', false)->get();

            $this->info("Encontrados {$unavailableSchedules->count()} horarios marcados como no disponibles");

            $fixed = 0;

            foreach ($unavailableSchedules as $schedule) {
                // Verificar si tiene citas activas (pending o confirmed)
                $activeAppointments = $schedule->appointments()
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();

                // Verificar si el horario ha expirado
                $date = Carbon::parse($schedule->date)->format('Y-m-d');
                $scheduleDateTime = Carbon::parse($date . ' ' . $schedule->start_time);
                $hasExpired = $scheduleDateTime->isPast();

                $this->line("Horario {$schedule->id} ({$schedule->date} {$schedule->start_time}):");
                $this->line("  - Citas activas: {$activeAppointments}");
                $this->line("  - Ha expirado: " . ($hasExpired ? 'Sí' : 'No'));

                // Si no tiene citas activas y no ha expirado, marcarlo como disponible
                if ($activeAppointments === 0 && !$hasExpired) {
                    $schedule->update(['is_available' => true]);
                    $this->info("  ✓ Marcado como disponible");
                    $fixed++;
                } else {
                    $this->line("  - Mantiene estado: no disponible");
                }
            }

            $this->info("Proceso completado. {$fixed} horarios corregidos.");

        } catch (\Exception $e) {
            $this->error('Error al procesar horarios: ' . $e->getMessage());
            Log::error('Error en comando schedules:fix-availability: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}