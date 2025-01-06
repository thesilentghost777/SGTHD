<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('Prime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_employe')->constrained('users')->onDelete('cascade');
            $table->string('libelle');
            $table->integer('montant');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('Prime');
    }
};
