<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventaires', function (Blueprint $table) {
            $table->id();
            $table->date('date_inventaire');
            $table->foreignId('produit_id')->constrained('produits');
            $table->integer('quantite_theorique');
            $table->integer('quantite_physique');
            $table->decimal('valeur_manquant', 10, 2);
            $table->foreignId('user_id')->constrained('users');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventaires');
    }
};
