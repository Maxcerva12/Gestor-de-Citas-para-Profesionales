<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario disponible para crear servicios de ejemplo
        $user = \App\Models\User::first();

        if (!$user) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        $services = [
            [
                'name' => 'Consulta General',
                'description' => 'Evaluación y diagnóstico general del estado oral del paciente',
                'price' => 50000,
                'duration' => 30,
            ],
            [
                'name' => 'Limpieza Dental',
                'description' => 'Limpieza profesional de los dientes',
                'price' => 80000,
                'duration' => 45,
            ],
            [
                'name' => 'Blanqueamiento Dental',
                'description' => 'Tratamiento estético para aclarar el color de los dientes',
                'price' => 200000,
                'duration' => 60,
            ],
        ];

        foreach ($services as $serviceData) {
            \App\Models\Service::create([
                'user_id' => $user->id,
                'name' => $serviceData['name'],
                'description' => $serviceData['description'],
                'price' => $serviceData['price'],
                'duration' => $serviceData['duration'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Services created successfully for user: ' . $user->name);
    }
}
