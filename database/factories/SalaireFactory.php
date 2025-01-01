<?php

namespace Database\Factories;

use App\Models\Salaire;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalaireFactory extends Factory
{
    protected $model = Salaire::class;

    public function definition()
    {
        return [
            'id_employe' => User::factory(),
            'somme' => $this->faker->numberBetween(50000, 500000),
            'somme_effective_mois' => function (array $attributes) {
                return $attributes['somme'];
            }
        ];
    }
}