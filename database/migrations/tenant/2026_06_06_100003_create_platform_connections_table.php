<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_connections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['linkedin', 'twitter', 'facebook', 'instagram', 'threads']);
            $table->string('platform_user_id');
            $table->string('platform_username')->nullable();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['connected', 'expired', 'disconnected', 'error'])->default('connected');
            $table->timestamp('last_posted_at')->nullable();
            $table->integer('follower_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_connections');
    }
};
