<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RealisticDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generando datos realistas para gráficas...');

        // Crear datos con patrones más realistas para los últimos 12 meses
        $this->createMonthlyUsers();
        $this->createMonthlyClients();
        $this->createMonthlyAppointments();

        $this->command->info('Datos realistas generados exitosamente');
    }

    private function createMonthlyUsers(): void
    {
        // Simular crecimiento gradual de usuarios profesionales
        $baseDate = Carbon::now()->subMonths(12);

        for ($month = 0; $month < 12; $month++) {
            $currentMonth = $baseDate->copy()->addMonths($month);

            // Más usuarios en ciertos meses (ej: inicio de año, septiembre)
            $usersThisMonth = match ($month) {
                0, 1 => rand(3, 6), // Enero-Febrero: más registros
                8, 9 => rand(4, 7), // Septiembre-Octubre: vuelta al trabajo
                11 => rand(1, 3),   // Diciembre: menos registros
                default => rand(2, 4)
            };

            for ($i = 0; $i < $usersThisMonth; $i++) {
                $randomDay = rand(1, $currentMonth->daysInMonth);
                $createdAt = $currentMonth->copy()->day($randomDay)->setTime(rand(8, 18), rand(0, 59));

                User::factory()->create([
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(rand(0, 15)),
                    'email_verified_at' => $createdAt->copy()->addMinutes(rand(10, 180))
                ]);
            }
        }

        $this->command->info('✓ Usuarios creados con distribución mensual realista');
    }

    private function createMonthlyClients(): void
    {
        // Simular registro de clientes con picos y valles
        $baseDate = Carbon::now()->subMonths(12);

        for ($month = 0; $month < 12; $month++) {
            $currentMonth = $baseDate->copy()->addMonths($month);

            // Más clientes después de que hay más profesionales
            $clientsThisMonth = match ($month) {
                0, 1, 2 => rand(5, 10),  // Primeros meses: pocos clientes
                3, 4, 5 => rand(8, 15),  // Crecimiento medio
                6, 7, 8 => rand(12, 20), // Verano: más actividad
                9, 10 => rand(15, 25),   // Otoño: pico de actividad
                11 => rand(8, 12)        // Diciembre: menos registros
            };

            for ($i = 0; $i < $clientsThisMonth; $i++) {
                $randomDay = rand(1, $currentMonth->daysInMonth);
                $createdAt = $currentMonth->copy()->day($randomDay)->setTime(rand(9, 20), rand(0, 59));

                Client::factory()->create([
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(rand(0, 30))
                ]);
            }
        }

        $this->command->info('✓ Clientes creados con patrones de crecimiento realistas');
    }

    private function createMonthlyAppointments(): void
    {
        $users = User::all();
        $clients = Client::all();

        if ($users->isEmpty() || $clients->isEmpty()) {
            $this->command->warn('No hay suficientes usuarios o clientes para crear citas');
            return;
        }

        $baseDate = Carbon::now()->subMonths(6);

        for ($month = 0; $month < 7; $month++) { // Incluir mes actual
            $currentMonth = $baseDate->copy()->addMonths($month);

            // Simular patrones de citas por mes
            $appointmentsThisMonth = match ($month) {
                0 => rand(20, 35),    // Hace 6 meses: comenzando
                1 => rand(30, 50),    // Crecimiento
                2 => rand(45, 70),    // Más actividad
                3 => rand(60, 90),    // Pico de actividad
                4 => rand(55, 85),    // Mantener alto
                5 => rand(70, 100),   // Más citas recientes
                6 => rand(40, 60)     // Mes actual (parcial)
            };

            for ($i = 0; $i < $appointmentsThisMonth; $i++) {
                $maxDay = ($month == 6) ? min(Carbon::now()->day, $currentMonth->daysInMonth) : $currentMonth->daysInMonth;
                $randomDay = rand(1, $maxDay);

                $appointmentStartTime = $currentMonth->copy()->day($randomDay);

                // Solo días laborables y horario laboral
                if ($appointmentStartTime->isWeekend()) {
                    $appointmentStartTime->next(Carbon::MONDAY);
                }

                $appointmentStartTime->setTime(rand(8, 17), rand(0, 59));
                $appointmentEndTime = $appointmentStartTime->copy()->addMinutes(rand(30, 90));

                // Crear la cita unos días antes
                $createdAt = $appointmentStartTime->copy()->subDays(rand(1, 14));

                // Crear un schedule primero
                $schedule = \App\Models\Schedule::create([
                    'user_id' => $users->random()->id,
                    'date' => $appointmentStartTime->format('Y-m-d'),
                    'start_time' => $appointmentStartTime->format('H:i:s'),
                    'end_time' => $appointmentEndTime->format('H:i:s'),
                    'is_available' => false, // Ya ocupado por la cita
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
                    'updated_at' => $createdAt->copy()->addDays(rand(0, 3))
                ]);
            }
        }

        $this->command->info('✓ Citas creadas con patrones de actividad realistas');
    }
}