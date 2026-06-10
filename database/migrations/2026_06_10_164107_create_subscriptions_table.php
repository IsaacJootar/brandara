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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->enum('plan', ['starter', 'pro', 'agency']);
            $table->enum('interval', ['monthly', 'yearly']);
            $table->string('currency', 3);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['active', 'past_due', 'cancelled', 'expired']);
            $table->enum('provider', ['flutterwave', 'paystack']);
            $table->string('provider_reference')->nullable();       // Payment reference
            $table->string('provider_subscription_id')->nullable(); // Recurring sub ID
            $table->string('provider_customer_id')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->index('workspace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
