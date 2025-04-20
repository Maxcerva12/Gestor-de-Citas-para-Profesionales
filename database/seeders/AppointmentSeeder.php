<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Crear 10 citas de prueba
        \App\Models\Appointment::factory(10)->create();
    }
}
