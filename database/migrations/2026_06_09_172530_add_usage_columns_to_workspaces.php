<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->unsignedInteger('ai_generations_used')->default(0)->after('language');
            $table->date('usage_reset_date')->nullable()->after('ai_generations_used');
        });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn(['ai_generations_used', 'usage_reset_date']);
        });
    }
};
