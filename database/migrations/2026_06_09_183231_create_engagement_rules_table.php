<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engagement_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->enum('type', ['auto_like', 'auto_comment']);
            $table->enum('platform', ['linkedin', 'twitter', 'instagram', 'facebook', 'threads']);
            $table->json('target_accounts')->nullable();
            $table->json('target_keywords')->nullable();
            $table->string('target_industry')->nullable();
            $table->unsignedSmallInteger('daily_limit')->default(20);
            $table->boolean('require_review')->default(true);
            $table->string('comment_tone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('actions_today')->default(0);
            $table->date('actions_reset_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagement_rules');
    }
};
