<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Crear clientes distribuidos en los últimos 6 meses
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        // Crear 50 clientes con fechas aleatorias para tener más datos en las gráficas
        for ($i = 0; $i < 50; $i++) {
            $randomDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            Client::factory()->create([
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
        }
    }
}
