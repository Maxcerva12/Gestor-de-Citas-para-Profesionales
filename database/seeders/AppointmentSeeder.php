<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Client;
use App\Models\Schedule;
use App\Models\Price; // Añade este modelo si existe
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Crear primero los datos relacionados
        $users = User::factory(10)->create();
        $clients = Client::factory(10)->create();

        // Crear horarios para cada usuario
        foreach ($users as $user) {
            Schedule::factory(5)->create([
                'user_id' => $user->id
            ]);
        }

        // Ahora crear las citas
        Appointment::factory(20)->create();
    }
}
