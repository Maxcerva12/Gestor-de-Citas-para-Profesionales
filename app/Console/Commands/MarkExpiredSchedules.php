<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MarkExpiredSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedules:mark-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como no disponibles los horarios que ya han pasado su fecha/hora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de marcado de horarios expirados...');

        try {
            // Obtener horarios que están disponibles pero ya han pasado
            $expiredSchedules = Schedule::where('is_available', true)
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->where('date', '<', $now->format('Y-m-d'))
                        ->orWhere(function ($subQuery) use ($now) {
                            $subQuery->where('date', '=', $now->format('Y-m-d'))
                                ->whereRaw("CONCAT(date, ' ', start_time) < ?", [$now->format('Y-m-d H:i:s')]);
                        });
                })
                ->get();

            $count = $expiredSchedules->count();

            if ($count > 0) {
                // Marcar como no disponibles
                Schedule::where('is_available', true)
                    ->where(function ($query) {
                        $now = Carbon::now();
                        $query->where('date', '<', $now->format('Y-m-d'))
                            ->orWhere(function ($subQuery) use ($now) {
                                $subQuery->where('date', '=', $now->format('Y-m-d'))
                                    ->whereRaw("CONCAT(date, ' ', start_time) < ?", [$now->format('Y-m-d H:i:s')]);
                            });
                    })
                    ->update(['is_available' => false]);

                $this->info("Se marcaron {$count} horarios como no disponibles.");

                // Log para seguimiento
                Log::info("Comando schedules:mark-expired ejecutado. Se marcaron {$count} horarios como no disponibles.");

                // Mostrar detalles de los horarios marcados
                foreach ($expiredSchedules as $schedule) {
                    $dateFormatted = Carbon::parse($schedule->date)->format('d/m/Y');
                    $this->line("- Horario ID {$schedule->id}: {$dateFormatted} {$schedule->start_time} - {$schedule->end_time} (Profesional: {$schedule->user->name})");
                }

            } else {
                $this->info('No se encontraron horarios expirados para marcar.');
            }

        } catch (\Exception $e) {
            $this->error('Error al procesar horarios expirados: ' . $e->getMessage());
            Log::error('Error en comando schedules:mark-expired: ' . $e->getMessage());
            return 1;
        }

        $this->info('Proceso completado exitosamente.');
        return 0;
    }
}