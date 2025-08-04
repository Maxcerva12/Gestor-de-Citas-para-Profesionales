<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Price>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Consulta Estándar', 'Consulta Urgente', 'Seguimiento', 'Primera Visita']),
            'amount' => fake()->randomFloat(2, 30, 200),
            'description' => fake()->sentence(),
            'is_active' => fake()->boolean(),
        ];
    }
}
