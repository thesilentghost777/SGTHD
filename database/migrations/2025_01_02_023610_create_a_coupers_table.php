<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Acouper', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_employe');
            $table->bigInteger('manquants')->default(0);
            $table->bigInteger('remboursement')->default(0);
            $table->bigInteger('pret')->default(0);
            $table->integer('caisse_sociale')->default(0);
            $table->date('date');
            $table->foreign('id_employe')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acouper');
    }
};
