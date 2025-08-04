<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = Carbon::now()->addDays(rand(1, 30))->setHour(rand(9, 17))->setMinute(0)->setSecond(0);
        $endTime = (clone $startTime)->addMinutes(rand(30, 120));

        // Obtener un precio aleatorio existente o crear uno nuevo si no existe ninguno
        $price = \App\Models\Price::inRandomOrder()->first() ?? \App\Models\Price::factory()->create();

        return [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => fake()->randomElement(['pending', 'confirmed', 'canceled']),
            'notes' => fake()->paragraph(),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'client_id' => \App\Models\Client::factory(),
            'user_id' => \App\Models\User::factory(),
            'schedule_id' => \App\Models\Schedule::factory(),
            'price_id' => $price->id,
            'amount' => $price->amount,
        ];
    }
}
