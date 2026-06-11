<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiPresenceResult extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand_id', 'provider', 'prompt', 'prompt_category',
        'appeared', 'position', 'sentiment', 'raw_response',
        'competitors_mentioned', 'queried_at',
    ];

    protected $casts = [
        'appeared' => 'boolean',
        'competitors_mentioned' => 'array',
        'queried_at' => 'datetime',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
