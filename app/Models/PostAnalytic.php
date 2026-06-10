<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostAnalytic extends Model
{
    use HasUuids;

    protected $fillable = [
        'post_id', 'brand_id', 'platform', 'fetched_date',
        'likes', 'comments', 'shares', 'reach', 'clicks', 'saves',
        'engagement_rate', 'source',
    ];

    protected $casts = [
        'fetched_date' => 'date',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function totalEngagements(): int
    {
        return $this->likes + $this->comments + $this->shares;
    }
}
