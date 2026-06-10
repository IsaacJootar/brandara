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
        Schema::create('billing_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('plan', ['starter', 'pro', 'agency']);
            $table->enum('interval', ['monthly', 'yearly']);
            $table->string('currency', 3);          // NGN, USD, GBP, EUR, GHS, KES, ZAR
            $table->decimal('amount', 10, 2);       // Display amount e.g. 19.00 or 30000.00
            $table->string('flutterwave_plan_id')->nullable(); // Set when live FW plan created
            $table->string('paystack_plan_id')->nullable();    // Set when live PS plan created
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['plan', 'interval', 'currency']);
            $table->index(['plan', 'interval']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_plans');
    }
};
