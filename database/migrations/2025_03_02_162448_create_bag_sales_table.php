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
        Schema::create('bag_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bag_reception_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_sold');
            $table->integer('quantity_unsold');
            $table->text('notes')->nullable();
            $table->boolean('is_recovered')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_sales');
    }
};
