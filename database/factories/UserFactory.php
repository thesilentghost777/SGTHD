<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'date_naissance' => fake()->unique()->date(),
            'code_secret' => fake()->randomElement([
                175,
                968,
                17986,
                365987,
            ]),
            'secteur' =>fake()->randomElement([
                'alimentation',
                'production',
                'glace',
                'administration',
            ]),
            'role' =>fake()->randomElement([
                'patissier',
                'boulanger',
                'pointeur',
                'chef production',
                'glaciere',
                'dg',
                'pdg',
                'alimentation',
            ]),
            'num_tel' =>fake()->unique->numberBetween(621000001,699999999),
            'avance_salaire' => 0,
            'annee_debut_service' => fake()->unique()->year($max = 'now'),
            'created_at' => fake()->unique()->dateTime(),
            'updated_at' => fake()->unique()->dateTime(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
