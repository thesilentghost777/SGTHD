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
        Schema::create('Porter', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('matiere');
            $table->unsignedBigInteger('facture');
            $table->tinyInteger('quantite');
            $table->foreign('matiere')->references('id')->on('Matiere')->onDelete('cascade');
            $table->foreign('facture')->references('code_fac')->on('Facture')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Porter');
    }
};
