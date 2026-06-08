<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand_id', 'name', 'type', 'pack_key', 'goal', 'key_message',
        'start_date', 'end_date', 'platforms', 'tone', 'ai_summary',
        'whatsapp_broadcast', 'status',
    ];

    protected $casts = [
        'platforms' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
