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
            'stripe_price_id' => 'price_' . fake()->unique()->regexify('[A-Za-z0-9]{24}'),
            'name' => fake()->randomElement(['Consulta Estándar', 'Consulta Urgente', 'Seguimiento', 'Primera Visita']),
            'amount' => fake()->randomFloat(2, 30, 200),
            'description' => fake()->sentence(),
            'is_active' => fake()->boolean(),
        ];
    }
}
