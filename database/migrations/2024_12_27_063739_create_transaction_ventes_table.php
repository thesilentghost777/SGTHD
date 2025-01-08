<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionVentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_ventes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produit')->nullable();
            $table->tinyInteger('serveur')->nullable();
            $table->Integer('quantite');
            $table->Integer('prix')->nullable();
            $table->date('date_vente')->nullable();
            $table->string('type');
            $table->string('monnaie')->nullable();
            $table->timestamps();
            $table->foreign('produit')->references('code_produit')->on('produit_fixes')
            ->onDelete('cascade');
            $table->foreign('serveur')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_ventes');
    }
}
