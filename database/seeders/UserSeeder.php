<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // Asegurar que siempre tenemos al menos un usuario predefinido para pruebas
        User::create([
            'name' => 'Maximiliano Seeder',
            'email' => 'mc349825@gmail.com',
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
        ]);

        // Generando 99 usuarios adicionales para llegar a 100
        for ($i = 0; $i < 99; $i++) {
            $profesiones = ['Médico', 'Psicólogo', 'Fisioterapeuta', 'Nutricionista', 'Dentista', 'Terapeuta'];
            $especialidades = [
                'Cardiología',
                'Neurología',
                'Psicología Clínica',
                'Nutrición Deportiva',
                'Ortodoncia',
                'Terapia de Pareja',
                'Pediatría',
                'Fisioterapia Deportiva'
            ];

            User::create([
                'name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'),
                'document_type' => $faker->randomElement(['DNI', 'Pasaporte', 'NIE']),
                'document_number' => $faker->unique()->numerify('########X'),
                'phone' => $faker->phoneNumber,
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'country' => $faker->country,
                'profession' => $faker->randomElement($profesiones),
                'especialty' => $faker->randomElement($especialidades),
                'description' => $faker->paragraph(2),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}
