<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EngagementRule extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand_id', 'type', 'platform',
        'target_accounts', 'target_keywords', 'target_industry',
        'daily_limit', 'require_review', 'comment_tone',
        'is_active', 'actions_today', 'actions_reset_date',
    ];

    protected $casts = [
        'target_accounts' => 'array',
        'target_keywords' => 'array',
        'require_review' => 'boolean',
        'is_active' => 'boolean',
        'actions_reset_date' => 'date',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(EngagementAction::class, 'rule_id');
    }

    public function isDailyLimitReached(): bool
    {
        if ($this->actions_reset_date === null || $this->actions_reset_date->lt(now()->startOfDay())) {
            return false;
        }

        return $this->actions_today >= $this->daily_limit;
    }

    public function incrementDailyActions(): void
    {
        if ($this->actions_reset_date === null || $this->actions_reset_date->lt(now()->startOfDay())) {
            $this->actions_today = 0;
            $this->actions_reset_date = now()->toDateString();
        }

        $this->increment('actions_today');
    }
}
