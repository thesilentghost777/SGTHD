<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('matiere_complexe', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matiere_id');
            $table->decimal('prix_complexe', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('matiere_id')->references('id')->on('Matiere')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('matiere_complexe');
    }
};
