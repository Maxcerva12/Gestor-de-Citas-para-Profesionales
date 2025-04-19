<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Obtener todos los usuarios
        $users = User::pluck('id')->toArray();

        // Horarios predefinidos
        $timeSlots = [
            ['09:00', '10:00'],
            ['10:00', '11:00'],
            ['11:00', '12:00'],
            ['12:00', '13:00'],
            ['15:00', '16:00'],
            ['16:00', '17:00'],
            ['17:00', '18:00'],
        ];

        // Crear los primeros 2 horarios específicos
        Schedule::create([
            'user_id' => 1,
            'date' => '2025-04-21 00:00:00',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'is_available' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schedule::create([
            'user_id' => 1,
            'date' => '2025-04-22 00:00:00',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'is_available' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Generar el resto de horarios hasta llegar a 100+
        $count = 0;
        $startDate = Carbon::now();

        while ($count < 98) {
            foreach ($users as $userId) {
                for ($i = 0; $i < 3; $i++) { // Crear 3 horarios por usuario
                    $date = $startDate->copy()->addDays(rand(1, 30));
                    $timeSlot = $faker->randomElement($timeSlots);

                    Schedule::create([
                        'user_id' => $userId,
                        'date' => $date->format('Y-m-d') . ' 00:00:00',
                        'start_time' => $timeSlot[0],
                        'end_time' => $timeSlot[1],
                        'is_available' => $faker->boolean(80), // 80% disponibles
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $count++;
                    if ($count >= 98) break;
                }
                if ($count >= 98) break;
            }
        }
    }
}
