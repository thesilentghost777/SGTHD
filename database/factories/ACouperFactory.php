<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ACouperFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_employe' => User::all()->random()->id,
            'manquants' => $this->faker->numberBetween(0, 1000), // Total des manquants
            'remboursement' => $this->faker->numberBetween(0, 1000), // Montant remboursé
            'pret' => $this->faker->numberBetween(0, 2000), // Montant emprunté
            'date' => $this->faker->dateTimeBetween('-6 months', 'now'), // Date dans les 6 derniers mois
        ];
    }
}
