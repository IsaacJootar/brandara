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
        Schema::create('post_analytics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->uuid('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');

            $table->string('platform');
            $table->date('fetched_date');        // date this snapshot was pulled

            // Core metrics
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('reach')->default(0);        // unique impressions
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('saves')->default(0);

            // Computed
            $table->decimal('engagement_rate', 5, 2)->default(0); // (likes+comments+shares)/reach * 100

            // Source: fake | linkedin | twitter | instagram | facebook | threads
            $table->string('source')->default('fake');

            $table->timestamps();

            $table->unique(['post_id', 'platform', 'fetched_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_analytics');
    }
};
