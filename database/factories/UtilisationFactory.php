<?php

namespace Database\Factories;

use App\Models\Utilisation;
use Illuminate\Database\Eloquent\Factories\Factory;

class UtilisationFactory extends Factory
{
    protected $model = Utilisation::class;

    public function definition()
    {
        return [
            'quantite' => $this->faker->numberBetween(1, 10),
            'created_at' => $this->faker->dateTimeBetween('2025-01-01', '2025-12-31'),
        ];
    }
}
