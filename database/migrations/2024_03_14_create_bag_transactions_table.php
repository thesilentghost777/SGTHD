<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bag_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bag_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['received', 'sold']);
            $table->integer('quantity');
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bag_transactions');
    }
};