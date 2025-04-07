<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('produits_recu_1', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produit_id');
            $table->foreign('produit_id')->references('code_produit')->on('Produit_fixes');
            $table->integer('quantite');
            $table->unsignedBigInteger('producteur_id');
            $table->foreign('producteur_id')->references('id')->on('users');
            $table->unsignedBigInteger('pointeur_id');
            $table->foreign('pointeur_id')->references('id')->on('users');
            $table->dateTime('date_reception');
            $table->text('remarques')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produits_recus');
    }
};
