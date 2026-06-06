<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_visibility_reports');
    }
};
