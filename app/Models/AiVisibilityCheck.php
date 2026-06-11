<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiVisibilityCheck extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand_id', 'website_url', 'results', 'manual_checks',
        'score', 'tier1_passed', 'tier2_passed', 'tier3_passed', 'scanned_at',
    ];

    protected $casts = [
        'results' => 'array',
        'manual_checks' => 'array',
        'scanned_at' => 'datetime',
        'score' => 'integer',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** Total checks passed. */
    public function totalPassed(): int
    {
        return $this->tier1_passed + $this->tier2_passed + $this->tier3_passed;
    }

    /** Readiness label based on score. */
    public function readinessLabel(): string
    {
        return match (true) {
            $this->score >= 80 => 'Strong',
            $this->score >= 55 => 'Moderate',
            $this->score >= 30 => 'Weak',
            default => 'Not ready',
        };
    }

    public function readinessColor(): string
    {
        return match (true) {
            $this->score >= 80 => '#16A34A',
            $this->score >= 55 => '#D97706',
            $this->score >= 30 => '#DC2626',
            default => '#94A3B8',
        };
    }
}
