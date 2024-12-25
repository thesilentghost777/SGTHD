<?php
namespace Database\Factories;

use App\Models\Daily_assignments;
use App\Models\User;
use App\Models\Produit_fixes;
use Illuminate\Database\Eloquent\Factories\Factory;

class Daily_assignmentsFactory extends Factory
{
    protected $model = Daily_assignments::class;

    public function definition()
    {
        return [
            'chef_production' => User::where('role', 'chef production')->inRandomOrder()->first()->id,
            'producteur' => User::where('secteur', 'production')->inRandomOrder()->first()->id,
            'produit' => Produit_fixes::inRandomOrder()->first()->code_produit,
            'expected_quantity' => $this->faker->numberBetween(10, 100),
            'assignment_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 0,
        ];
    }
}