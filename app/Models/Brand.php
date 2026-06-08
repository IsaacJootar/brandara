<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasUuids;

    protected $fillable = [
        'workspace_id', 'name', 'slug', 'tagline', 'description',
        'vision', 'mission', 'values', 'target_audience', 'negative_brief',
        'positioning', 'primary_color', 'secondary_color', 'font_preference',
        'logo_path', 'brand_voice', 'voice_samples_count', 'default_tone', 'language',
    ];

    protected $casts = [
        'values'    => 'array',
        'brand_voice' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function platformConnections(): HasMany
    {
        return $this->hasMany(PlatformConnection::class);
    }

    public function contentPillars(): HasMany
    {
        return $this->hasMany(ContentPillar::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function mediaFiles(): HasMany
    {
        return $this->hasMany(MediaFile::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function aiVisibilityReports(): HasMany
    {
        return $this->hasMany(AiVisibilityReport::class);
    }
}
