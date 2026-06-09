<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// This migration is intentionally empty — the review queue
// is handled via the engagement_actions table using status='pending'.
// Keeping this file to maintain migration sequence integrity.
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
