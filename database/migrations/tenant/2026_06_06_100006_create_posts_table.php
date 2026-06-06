<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('content_pillar_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->enum('input_type', ['topic', 'voice_note', 'pdf', 'transcript', 'product', 'manual'])->default('topic');
            $table->text('raw_input')->nullable();
            $table->boolean('ai_generated')->default(false);
            $table->enum('variation_selected', ['authority', 'story', 'bold'])->nullable();
            $table->json('platform_contents')->nullable();
            $table->string('tone')->nullable();
            $table->json('media_ids')->nullable();
            $table->enum('status', ['draft', 'in_review', 'scheduled', 'published', 'failed', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->json('live_post_urls')->nullable();
            $table->boolean('is_evergreen')->default(false);
            $table->timestamp('last_recycled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
