<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador principal (siempre actual) - solo si no existe
        User::firstOrCreate(
            ['email' => 'joselopez132002@gmail.com'],
            [
                'name' => 'Jose Lopez',
                'password' => Hash::make('password123'),
                'document_type' => 'DNI',
                'document_number' => '12345678A',
                'phone' => '612345678',
                'address' => 'Calle Principal 123',
                'city' => 'Medellín',
                'country' => 'Colombia',
                'profession' => 'Médico',
                'especialty' => 'Cardiología',
                'description' => 'Profesional con más de 10 años de experiencia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'email_verified_at' => Carbon::now(),
            ]
        );

        // Crear usuarios adicionales distribuidos en el tiempo
        $startDate = Carbon::now()->subMonths(8); // Más tiempo para usuarios principales
        $endDate = Carbon::now()->subWeek(); // Hasta hace una semana

        // Crear 15 usuarios profesionales con fechas distribuidas
        for ($i = 0; $i < 15; $i++) {
            $randomDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            User::factory()->create([
                'created_at' => $randomDate,
                'updated_at' => $randomDate->copy()->addDays(rand(0, 30)), // Pueden haber sido actualizados después
                'email_verified_at' => $randomDate->copy()->addMinutes(rand(5, 120))
            ]);
        }
    }
}
