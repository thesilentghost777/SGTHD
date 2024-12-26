<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Commande', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('libelle', 50);
            $table->dateTime('date_commande');
            $table->unsignedBigInteger('produit')->nullable();
            $table->integer('quantite');
            $table->foreign('produit')->references('code_produit')->on('Produit_fixes');
            $table->string('categorie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Commande');
    }
};
