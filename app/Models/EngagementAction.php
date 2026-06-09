<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngagementAction extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand_id', 'rule_id', 'type', 'platform',
        'target_post_id', 'target_account', 'target_post_excerpt',
        'comment_body', 'status', 'failure_reason', 'posted_at',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(EngagementRule::class, 'rule_id');
    }
}
