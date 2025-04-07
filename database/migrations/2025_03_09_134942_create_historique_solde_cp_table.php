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
        Schema::create('historique_solde_cp', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 15, 2);
            $table->enum('type_operation', ['versement', 'depense', 'ajustement']);
            $table->unsignedBigInteger('operation_id')->nullable();
            $table->decimal('solde_avant', 15, 2);
            $table->decimal('solde_apres', 15, 2);
            $table->unsignedBigInteger('user_id');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_solde_cp');
    }
};
