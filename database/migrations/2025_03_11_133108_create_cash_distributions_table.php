<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Vendeuse
            $table->date('date');
            $table->decimal('bill_amount', 15, 2); // Montant en billets
            $table->decimal('initial_coin_amount', 15, 2); // Montant initial en monnaie
            $table->decimal('final_coin_amount', 15, 2)->nullable(); // Montant final en monnaie déclaré
            $table->decimal('deposited_amount', 15, 2)->nullable(); // Montant versé
            $table->decimal('sales_amount', 15, 2)->default(0); // Montant des ventes
            $table->decimal('missing_amount', 15, 2)->nullable(); // Montant manquant calculé
            $table->enum('status', ['en_cours', 'cloture'])->default('en_cours');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable(); // Admin qui a clôturé
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_distributions');
    }
};
