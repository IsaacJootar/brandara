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
        Schema::create('trend_signals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id');
            $table->enum('category', ['industry', 'format', 'competitor']);
            $table->string('platform')->default('all'); // linkedin, twitter, instagram, tiktok, or 'all'
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('strength')->default(50); // 1–100 signal strength
            $table->json('tags')->nullable();        // related hashtags / keywords
            $table->string('source')->default('fake'); // fake | ai | api
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->index(['brand_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trend_signals');
    }
};
