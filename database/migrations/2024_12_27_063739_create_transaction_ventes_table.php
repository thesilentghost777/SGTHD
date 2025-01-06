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
            $table->unsignedBigInteger('produit');
            $table->unsignedBigInteger('serveur');
            $table->Integer('quantite');
            $table->Integer('prix');
            $table->date('date_vente');
            $table->string('type');
            $table->string('monnaie');
            $table->timestamps();
            $table->foreign('produit')->references('code_produit')->on('Produit_fixes')
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
