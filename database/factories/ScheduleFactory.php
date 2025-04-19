<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Schedule;

class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = Carbon::now()->addDays(rand(1, 30))->startOfDay();
        $startTime = fake()->time('H:i:00', '17:00:00');
        $endTime = fake()->time('H:i:00', '20:00:00');

        return [
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'user_id' => \App\Models\User::factory(),
            'is_available' => true,
        ];
    }
}
