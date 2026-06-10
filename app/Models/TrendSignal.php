<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrendSignal extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand_id', 'category', 'platform', 'title',
        'description', 'strength', 'tags', 'source', 'fetched_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'fetched_at' => 'datetime',
        'strength' => 'integer',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
