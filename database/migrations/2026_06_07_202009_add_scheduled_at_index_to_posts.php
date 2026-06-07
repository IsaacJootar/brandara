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
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'scheduled_at'], 'posts_status_scheduled_at_idx');
            $table->index(['brand_id', 'status'], 'posts_brand_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_scheduled_at_idx');
            $table->dropIndex('posts_brand_status_idx');
        });
    }
};
