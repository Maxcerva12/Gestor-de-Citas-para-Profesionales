<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class CancelExpiredAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancelar automáticamente las citas pendientes que ya pasaron su fecha y hora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando citas pendientes vencidas...');

        // Buscar citas pendientes cuya fecha/hora de inicio ya pasó
        $expiredAppointments = Appointment::where('status', 'pending')
            ->where('start_time', '<', Carbon::now())
            ->get();

        if ($expiredAppointments->isEmpty()) {
            $this->info('No se encontraron citas vencidas.');
            return 0;
        }

        $count = 0;
        foreach ($expiredAppointments as $appointment) {
            $appointment->update([
                'status' => 'canceled',
                'cancellation_reason' => 'Cita cancelada automáticamente por el sistema debido a que la fecha y hora programada ya pasó sin confirmación.',
                'cancelled_by' => 'system',
                'cancelled_at' => now(),
            ]);

            $count++;

            $this->line("✓ Cita ID {$appointment->id} cancelada (Cliente: {$appointment->client->name}, Fecha: {$appointment->start_time->format('d/m/Y H:i')})");
        }

        $this->info("✓ Se cancelaron {$count} cita(s) vencida(s) exitosamente.");

        return 0;
    }
}
