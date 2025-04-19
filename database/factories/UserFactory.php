<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Provider\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'document_type' => fake()->randomElement(['DNI', 'Pasaporte', 'NIE']),
            'document_number' => fake()->unique()->numerify('########X'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'profession' => fake()->randomElement(['Médico', 'Psicólogo', 'Fisioterapeuta', 'Nutricionista', 'Dentista', 'Terapeuta']),
            'especialty' => fake()->randomElement([
                'Cardiología',
                'Neurología',
                'Psicología Clínica',
                'Nutrición Deportiva',
                'Ortodoncia',
                'Terapia de Pareja',
                'Pediatría',
                'Fisioterapia Deportiva'
            ]),
            'description' => fake()->paragraph(2),
            'remember_token' => Str::random(10),
            'avatar_url' => 'https://i.pravatar.cc/300?u=' . Str::random(10), // Genera un avatar único usando Pravatar
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
