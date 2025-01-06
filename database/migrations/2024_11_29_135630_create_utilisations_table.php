<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('Utilisation', function (Blueprint $table) {
        $table->id();
        $table->string('id_lot', 20);
        $table->index('id_lot');
        $table->unsignedBigInteger('produit');  // Type correspond maintenant à code_produit
        $table->unsignedBigInteger('matierep');
        $table->unsignedBigInteger('producteur');
        $table->decimal('quantite_produit', 10, 2);
        $table->decimal('quantite_matiere', 10, 3);
        $table->string('unite_matiere');
        $table->timestamps();

        $table->foreign('produit')->references('code_produit')->on('Produit_fixes');
        $table->foreign('matierep')->references('id')->on('Matiere');
        $table->foreign('producteur')->references('id')->on('users');
    });
}

 /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Utilisation');
    }
};
