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
        Schema::create('Produit_fixes', function (Blueprint $table) {
            $table->id('code_produit');  // Utilise id() qui crée un unsignedBigInteger AUTO_INCREMENT
            $table->string('nom', 50);
            $table->smallInteger('prix');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
