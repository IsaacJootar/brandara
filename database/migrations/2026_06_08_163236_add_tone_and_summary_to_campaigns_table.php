<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('tone')->nullable()->after('platforms');
            $table->text('ai_summary')->nullable()->after('tone');
            $table->string('whatsapp_broadcast')->nullable()->after('ai_summary');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['tone', 'ai_summary', 'whatsapp_broadcast']);
        });
    }
};
