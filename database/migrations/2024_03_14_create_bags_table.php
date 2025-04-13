<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity');
            $table->integer('alert_threshold')->default(100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bags');
    }
};