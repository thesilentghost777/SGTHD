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
        Schema::create('Horaire', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('employe')->nullable();
            $table->dateTime('arrive')->nullable();
            $table->dateTime('depart')->nullable();
            $table->foreign('employe')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Horaire');
    }
};
