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
        Schema::create('ai_visibility_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id');
            $table->string('website_url');
            $table->json('results');          // map of check_key => pass|fail|pending
            $table->json('manual_checks');    // user-confirmed checks
            $table->unsignedTinyInteger('score')->default(0);        // 0-100
            $table->unsignedTinyInteger('tier1_passed')->default(0);
            $table->unsignedTinyInteger('tier2_passed')->default(0);
            $table->unsignedTinyInteger('tier3_passed')->default(0);
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_visibility_checks');
    }
};
