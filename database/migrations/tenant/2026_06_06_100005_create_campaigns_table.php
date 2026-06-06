<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['builtin', 'custom'])->default('custom');
            $table->string('pack_key')->nullable();
            $table->text('goal')->nullable();
            $table->text('key_message')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('platforms');
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
