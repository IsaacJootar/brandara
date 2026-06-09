<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engagement_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->uuid('rule_id');
            $table->foreign('rule_id')->references('id')->on('engagement_rules')->onDelete('cascade');

            $table->enum('type', ['like', 'comment']);
            $table->enum('platform', ['linkedin', 'twitter', 'instagram', 'facebook', 'threads']);

            // The post/content that was engaged with
            $table->string('target_post_id')->nullable();   // platform post ID
            $table->string('target_account')->nullable();   // whose post it was
            $table->text('target_post_excerpt')->nullable(); // first 280 chars of the post

            // For comments
            $table->text('comment_body')->nullable();

            // Status: pending (review queue) | approved | posted | skipped | failed
            $table->enum('status', ['pending', 'approved', 'posted', 'skipped', 'failed'])->default('pending');
            $table->string('failure_reason')->nullable();
            $table->timestamp('posted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagement_actions');
    }
};
