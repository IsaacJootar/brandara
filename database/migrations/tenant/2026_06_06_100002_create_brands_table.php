<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->json('values')->nullable();
            $table->text('target_audience')->nullable();
            $table->text('negative_brief')->nullable();
            $table->text('positioning')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('font_preference')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('voice_dna')->nullable();
            $table->integer('voice_samples_count')->default(0);
            $table->enum('default_tone', ['corporate', 'professional', 'founder', 'african', 'friendly', 'educational', 'bold', 'luxury'])->default('professional');
            $table->enum('language', ['en', 'fr'])->default('en');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
