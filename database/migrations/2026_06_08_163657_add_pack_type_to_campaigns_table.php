<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum('builtin','custom') to string so 'pack' is also valid.
        // SQLite does not support ALTER COLUMN on enums — recreated as string.
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('type')->default('custom')->change();
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('type')->default('custom')->change();
        });
    }
};
