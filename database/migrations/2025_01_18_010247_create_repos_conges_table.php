<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('repos_conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('users')->onDelete('cascade');
            $table->enum('jour', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche']);
            $table->integer('conges')->nullable();
            $table->date('debut_c')->nullable();
            $table->enum('raison_c', ['maladie', 'evenement', 'accouchement', 'autre'])->nullable();
            $table->text('autre_raison')->nullable();
            $table->timestamps();

            // Ensure one entry per employee
            $table->unique('employe_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('repos_conges');
    }
};
