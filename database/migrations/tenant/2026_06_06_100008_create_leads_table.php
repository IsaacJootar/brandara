<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['linkedin', 'twitter', 'facebook', 'instagram', 'threads']);
            $table->string('platform_user_id');
            $table->string('name')->nullable();
            $table->string('headline')->nullable();
            $table->string('company')->nullable();
            $table->string('profile_url')->nullable();
            $table->enum('tag', ['warm_lead', 'prospect', 'client', 'partner', 'other'])->nullable();
            $table->text('notes')->nullable();
            $table->date('follow_up_at')->nullable();
            $table->integer('total_engagements')->default(0);
            $table->timestamp('last_engaged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
