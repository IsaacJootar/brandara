<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('owner_email');
            $table->string('country');
            $table->string('timezone')->default('Africa/Lagos');
            $table->enum('plan', ['starter', 'pro', 'agency'])->default('starter');
            $table->timestamp('trial_ends_at')->nullable();
            $table->enum('subscription_status', ['trialing', 'active', 'past_due', 'cancelled'])->default('trialing');
            $table->string('paystack_customer_id')->nullable();
            $table->string('flutterwave_customer_id')->nullable();
            $table->enum('language', ['en', 'fr'])->default('en');
            $table->timestamps();
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
