<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Salaire;
use App\Models\User;

class SalaireSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            Salaire::factory()->create([
                'id_employe' => $user->id
            ]);
        });
    }
}