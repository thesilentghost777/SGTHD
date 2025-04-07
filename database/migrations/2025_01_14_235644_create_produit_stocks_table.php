<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('produit_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_produit');
            $table->foreign('id_produit')->references('code_produit')->on('Produit_fixes')->onDelete('cascade');
            $table->integer('quantite_en_stock')->default(0);
            $table->integer('quantite_invendu')->default(0);
            $table->integer('quantite_avarie')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produit_stocks');
    }
};
