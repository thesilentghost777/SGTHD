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
        Schema::create('cashier_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('initial_cash', 12, 2);
            $table->decimal('initial_change', 12, 2);
            $table->decimal('initial_mobile_balance', 12, 2);
            $table->decimal('final_cash', 12, 2)->nullable();
            $table->decimal('final_change', 12, 2)->nullable();
            $table->decimal('final_mobile_balance', 12, 2)->nullable();
            $table->decimal('cash_remitted', 12, 2)->nullable();
            $table->decimal('total_withdrawals', 12, 2)->default(0);
            $table->decimal('discrepancy', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('end_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_sessions');
    }
};
