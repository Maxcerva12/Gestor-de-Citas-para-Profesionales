<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios distribuidos en los últimos 6 meses
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        // Crear 10 usuarios con fechas aleatorias
        for ($i = 0; $i < 10; $i++) {
            $randomDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            User::factory()->create([
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
                'email_verified_at' => $randomDate->addMinutes(rand(5, 60))
            ]);
        }
    }
}
