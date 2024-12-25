<?php

namespace Database\Factories;
use App\Models\Produit_fixes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\production_suggerer_par_jour>
 */
class Production_suggerer_par_jourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'produit' => Produit_fixes::inRandomOrder()->first()->code_produit,
            'quantity' => $this->faker->numberBetween(10, 100),
            'day' => $this->faker->randomElement([
                'lundi',
                'mardi',
                'mercredi',
                'jeudi',
                'vendredi',
                'samedi',
                'dimanche',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
