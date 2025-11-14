<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
                // Primero crear usuarios y permisos básicos
            UserSeeder::class,
            SchedulePermissionsSeeder::class,
            CustomPermissionsSeeder::class,
                // Luego los datos maestros necesarios
            ServiceSeeder::class,

                // Luego generar datos realistas distribuidos en el tiempo
            RealisticDataSeeder::class,

                // Finalmente usuarios y clientes adicionales para pruebas
            TestUserSeeder::class,
            ClientSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
