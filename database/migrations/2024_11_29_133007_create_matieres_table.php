<?php

use App\Enums\UniteMinimale;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('Matiere', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('unite_minimale'); // Changé de enum à string
            $table->string('unite_classique');
            $table->decimal('quantite_par_unite', 10, 3);
            $table->decimal('quantite', 10, 2);
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('prix_par_unite_minimale', 10, 4);
            $table->timestamps();
        });
    // Ajouter par défaut une matière "produit avarier"
    DB::table('Matiere')->insert([
        'nom' => 'produit avarier',
        'unite_minimale' => 'unité',
        'unite_classique' => 'unité',
        'quantite_par_unite' => 1.000,
        'quantite' => 0.00,
        'prix_unitaire' => 0.00,
        'prix_par_unite_minimale' => 0.0000,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    }

    public function down()
    {
        Schema::dropIfExists('Matiere');
    }
};
