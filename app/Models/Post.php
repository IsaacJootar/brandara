<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand_id', 'content_pillar_id', 'campaign_id', 'created_by', 'approved_by',
        'title', 'input_type', 'raw_input', 'ai_generated', 'variation_selected',
        'platform_contents', 'tone', 'media_ids', 'status',
        'scheduled_at', 'published_at', 'failure_reason', 'retry_count',
        'live_post_urls', 'is_evergreen', 'last_recycled_at',
    ];

    protected $casts = [
        'platform_contents' => 'array',
        'media_ids'         => 'array',
        'live_post_urls'    => 'array',
        'ai_generated'      => 'boolean',
        'is_evergreen'      => 'boolean',
        'scheduled_at'      => 'datetime',
        'published_at'      => 'datetime',
        'last_recycled_at'  => 'datetime',
    ];

    public function brand(): BelongsTo        { return $this->belongsTo(Brand::class); }
    public function contentPillar(): BelongsTo { return $this->belongsTo(ContentPillar::class); }
    public function campaign(): BelongsTo      { return $this->belongsTo(Campaign::class); }
    public function creator(): BelongsTo       { return $this->belongsTo(User::class, 'created_by'); }
    public function approver(): BelongsTo      { return $this->belongsTo(User::class, 'approved_by'); }
}
