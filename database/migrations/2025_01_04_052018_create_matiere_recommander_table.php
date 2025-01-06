<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Matiere_recommander', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produit');
            $table->unsignedBigInteger('matierep');
            $table->Integer('quantitep');
            $table->decimal('quantite', 10, 3);
            $table->string('unite');
            $table->timestamps();
            $table->foreign('produit')->references('code_produit')->on('Produit_fixes');
            $table->foreign('matierep')->references('id')->on('Matiere');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Matiere_recommander');
    }
};
