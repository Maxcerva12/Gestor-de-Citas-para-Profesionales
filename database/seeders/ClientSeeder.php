<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // Cliente predefinido para pruebas
        Client::create([
            'name' => 'Jose Seeder',
            'email' => 'joseLopez54@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '3254789654',
            'address' => 'Avenida el Poblado 45',
            'city' => 'Santa Marta',
            'country' => 'Colombia',
        ]);

        // Generando 99 clientes adicionales
        for ($i = 0; $i < 99; $i++) {
            Client::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'),
                'phone' => $faker->phoneNumber,
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'country' => $faker->country,
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}
