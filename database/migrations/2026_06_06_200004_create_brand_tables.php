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

        Schema::create('content_pillars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('goal', ['authority', 'trust', 'awareness', 'conversion']);
            $table->string('color');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['builtin', 'custom'])->default('custom');
            $table->string('pack_key')->nullable();
            $table->text('goal')->nullable();
            $table->text('key_message')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('platforms');
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->timestamps();
        });

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

        Schema::create('media_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('filename');
            $table->string('storage_path');
            $table->string('mime_type');
            $table->integer('file_size_kb');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['linkedin', 'twitter', 'facebook', 'instagram', 'threads']);
            $table->string('platform_user_id');
            $table->string('name')->nullable();
            $table->string('headline')->nullable();
            $table->string('company')->nullable();
            $table->string('profile_url')->nullable();
            $table->enum('tag', ['warm_lead', 'prospect', 'client', 'partner', 'other'])->nullable();
            $table->text('notes')->nullable();
            $table->date('follow_up_at')->nullable();
            $table->integer('total_engagements')->default(0);
            $table->timestamp('last_engaged_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_visibility_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->enum('ai_system', ['chatgpt', 'perplexity', 'gemini', 'google_ai', 'claude']);
            $table->text('query');
            $table->text('response_text');
            $table->boolean('brand_mentioned')->default(false);
            $table->integer('mention_position')->nullable();
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable();
            $table->json('topics')->nullable();
            $table->date('report_date');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->json('channels');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('ai_visibility_reports');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('content_pillars');
        Schema::dropIfExists('platform_connections');
    }
};
