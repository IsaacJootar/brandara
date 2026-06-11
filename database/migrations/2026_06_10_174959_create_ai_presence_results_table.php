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
        Schema::create('ai_presence_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id');
            $table->enum('provider', ['claude', 'chatgpt', 'gemini', 'perplexity']);
            $table->text('prompt');
            $table->string('prompt_category'); // discovery|local_intent|trust|comparison|consideration
            $table->boolean('appeared')->default(false);
            $table->unsignedTinyInteger('position')->nullable(); // 1-10 position in answer
            $table->enum('sentiment', ['positive', 'neutral', 'negative', 'not_mentioned'])->default('not_mentioned');
            $table->text('raw_response')->nullable();
            $table->json('competitors_mentioned')->nullable(); // other brands in the answer
            $table->timestamp('queried_at')->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->index(['brand_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_presence_results');
    }
};
