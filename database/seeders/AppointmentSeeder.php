<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios y clientes existentes
        $users = User::all();
        $clients = Client::all();

        if ($users->isEmpty() || $clients->isEmpty()) {
            $this->command->warn('No hay usuarios o clientes disponibles para crear citas');
            return;
        }

        // Crear citas distribuidas en los últimos 4 meses
        $startDate = Carbon::now()->subMonths(4);
        $endDate = Carbon::now()->addDays(30); // Incluir algunas citas futuras

        // Crear 80 citas con fechas distribuidas
        for ($i = 0; $i < 80; $i++) {
            $appointmentStartTime = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            // Asegurar que las citas sean en horario laboral (9 AM - 6 PM)
            $appointmentStartTime->setHour(rand(9, 17))->setMinute(rand(0, 59));
            $appointmentEndTime = $appointmentStartTime->copy()->addMinutes(rand(30, 120));

            $createdAt = $appointmentStartTime->copy()->subDays(rand(1, 7));

            // Crear un schedule primero
            $schedule = Schedule::create([
                'user_id' => $users->random()->id,
                'date' => $appointmentStartTime->format('Y-m-d'),
                'start_time' => $appointmentStartTime->format('H:i:s'),
                'end_time' => $appointmentEndTime->format('H:i:s'),
                'is_available' => false,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            Appointment::create([
                'user_id' => $schedule->user_id,
                'client_id' => $clients->random()->id,
                'schedule_id' => $schedule->id,
                'start_time' => $appointmentStartTime,
                'end_time' => $appointmentEndTime,
                'status' => fake()->randomElement(['pending', 'confirmed', 'completed']),
                'notes' => fake()->sentence(),
                'payment_status' => 'pending',
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(0, 5)),
            ]);
        }

        $this->command->info('Se crearon 80 citas distribuidas en los últimos 4 meses');
    }
}
