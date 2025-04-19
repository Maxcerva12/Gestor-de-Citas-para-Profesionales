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
        // Deshabilitar temporalmente los observers
        Appointment::unsetEventDispatcher();

        $faker = Faker::create();

        // Obtener IDs existentes de las tablas relacionadas
        $userIds = User::pluck('id')->toArray();
        $clientIds = Client::pluck('id')->toArray();
        $scheduleIds = Schedule::where('is_available', 1)->pluck('id')->toArray();

        // Si existe un modelo Price, obtener sus IDs, de lo contrario usar [1]
        $priceIds = class_exists('App\Models\Price') ?
            Price::pluck('id')->toArray() : [1];

        // Si no hay horarios disponibles, no podemos crear citas
        if (empty($scheduleIds)) {
            Log::warning('No hay horarios disponibles para crear citas');
            return;
        }

        // Crear la cita predefinida solo si los IDs existen
        if (
            in_array(1, $userIds) && in_array(1, $clientIds) &&
            in_array(1, $scheduleIds) && in_array(1, $priceIds)
        ) {

            $schedule = Schedule::find(1);
            if ($schedule) {
                Appointment::create([
                    'user_id' => 1,
                    'client_id' => 1,
                    'schedule_id' => 1,
                    'start_time' => '2025-04-21 10:00:00',
                    'end_time' => '2025-04-21 11:00:00',
                    'status' => 'pending',
                    'price_id' => 1,
                    'payment_status' => 'paid',
                    'stripe_payment_intent' => 'pi_3RFdlDEOd8VWcTPx0KFJpmUC',
                    'stripe_checkout_session' => 'cs_test_a15jM0xTjx31Tmlbuk5XxdKo9yqXcVvvwY5yXm2iSmvSC7zICGBQ7wQuE9',
                ]);

                // Marcar el horario como no disponible
                $schedule->update(['is_available' => 0]);
            }
        }

        // Estatus posibles
        $statuses = ['pending', 'confirmed', 'completed', 'canceled'];
        $paymentStatuses = ['paid', 'pending', 'failed', 'refunded'];

        // Contador para citas creadas
        $count = 0;
        $maxAttempts = 200; // Limitar intentos para evitar bucle infinito
        $attempts = 0;

        // Generar citas adicionales
        while ($count < 99 && $attempts < $maxAttempts) {
            $attempts++;

            // Si nos quedamos sin horarios disponibles, salir del loop
            if (empty($scheduleIds)) {
                break;
            }

            // Seleccionar IDs aleatorios de las colecciones existentes
            $scheduleId = $faker->randomElement($scheduleIds);
            $schedule = Schedule::find($scheduleId);

            if (!$schedule) {
                // Si el horario ya no existe, eliminarlo del array y continuar
                $scheduleIds = array_diff($scheduleIds, [$scheduleId]);
                continue;
            }

            $userId = $schedule->user_id;

            // Verificar que el usuario existe
            if (!in_array($userId, $userIds)) {
                // Si el usuario no existe, eliminar este horario y continuar
                $scheduleIds = array_diff($scheduleIds, [$scheduleId]);
                continue;
            }

            // Seleccionar un cliente aleatorio
            if (empty($clientIds)) {
                break; // Si no hay clientes, no podemos crear citas
            }

            $clientId = $faker->randomElement($clientIds);

            // Seleccionar un precio aleatorio
            if (empty($priceIds)) {
                $priceId = null; // Si no hay precios, usar null si la columna lo permite
            } else {
                $priceId = $faker->randomElement($priceIds);
            }

            $startTime = Carbon::parse($schedule->date)->setTimeFromTimeString($schedule->start_time);
            $endTime = Carbon::parse($schedule->date)->setTimeFromTimeString($schedule->end_time);

            try {
                Appointment::create([
                    'user_id' => $userId,
                    'client_id' => $clientId,
                    'schedule_id' => $scheduleId,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $faker->randomElement($statuses),
                    'notes' => $faker->optional(0.7)->paragraph(1),
                    'price_id' => $priceId,
                    'payment_status' => $faker->randomElement($paymentStatuses),
                    'stripe_payment_intent' => $faker->optional(0.7)->regexify('pi_[A-Za-z0-9]{24}'),
                    'stripe_checkout_session' => $faker->optional(0.7)->regexify('cs_test_[A-Za-z0-9]{40}'),
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                    'updated_at' => $faker->dateTimeBetween('-6 months', 'now'),
                ]);

                // Marcar el horario como no disponible y removerlo de la lista
                $schedule->update(['is_available' => 0]);
                $scheduleIds = array_diff($scheduleIds, [$scheduleId]);

                $count++;
            } catch (\Exception $e) {
                // Si hay un error, eliminar este horario de la lista y continuar
                $scheduleIds = array_diff($scheduleIds, [$scheduleId]);
                Log::warning("Error al crear cita: " . $e->getMessage());
            }
        }

        if ($count < 99) {
            Log::warning("Solo se pudieron crear $count citas de 99 requeridas");
        }
    }
}
