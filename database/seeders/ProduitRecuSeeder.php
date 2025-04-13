<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProduitRecu;
use App\Models\User;
use App\Models\ProduitFixe;

class ProduitRecuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        ProduitRecu::factory(50)->create(); // Génère 50 entrées dans la table Produit_recu
    }
}
