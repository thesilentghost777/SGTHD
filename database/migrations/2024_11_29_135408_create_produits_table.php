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
        Schema::create('Produit', function (Blueprint $table) {
            $table->unsignedBigInteger('code_produit')->autoIncrement();
            $table->unsignedBigInteger('producteur');
            $table->string('nom', 50);
            $table->unsignedBigInteger('prix');
            $table->smallInteger('quantite');
            $table->foreign('producteur')->references('id')->on('users')->onDelete('cascade');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Produit');
    }
};
