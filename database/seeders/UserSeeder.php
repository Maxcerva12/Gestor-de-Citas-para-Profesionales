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
        User::create([
            'name' => 'jose lopez',
            'email' => 'joselopez132002@gmail.com',
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

    }
}
