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
        Schema::create('Versement_csg', function (Blueprint $table) {
            $table->unsignedBigInteger('code_vcsg')->autoIncrement();
            $table->string('libelle', 50);
            $table->date('date');
            $table->Integer('somme');
            $table->unsignedBigInteger('verseur');
            $table->unsignedBigInteger('encaisseur');
            $table->foreign('verseur')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('encaisseur')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Versement_csg');
    }
};
