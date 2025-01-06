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
    }

    public function down()
    {
        Schema::dropIfExists('Matiere');
    }
};
