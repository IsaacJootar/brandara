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
        'website_url', 'settings',
    ];

    protected $casts = [
        'values' => 'array',
        'brand_voice' => 'array',
        'settings' => 'array',
    ];

    /**
     * Default settings for every brand.
     * Merge these with stored settings so new keys are always present.
     */
    public static function defaultSettings(): array
    {
        return [
            // Engagement automation
            'engagement_enabled' => false,  // opt-in, off by default
            'engagement_scan_frequency' => 'daily', // daily | twice_daily | weekly | twice_weekly

            // Publishing
            'default_post_time' => '09:00',
            'timezone' => 'Africa/Lagos',
            'evergreen_recycling' => false,

            // Notifications
            'notify_post_failed' => true,
            'notify_post_published' => false,
            'notify_lead_captured' => true,
            'notify_trial_expiring' => true,
        ];
    }

    /**
     * Get a specific setting value, falling back to the default.
     */
    public function setting(string $key): mixed
    {
        $stored = $this->settings ?? [];
        $defaults = self::defaultSettings();

        return $stored[$key] ?? $defaults[$key] ?? null;
    }

    /**
     * Update one or more settings.
     *
     * @param  array<string, mixed>  $values
     */
    public function updateSettings(array $values): void
    {
        $current = $this->settings ?? [];
        $this->update(['settings' => array_merge($current, $values)]);
    }

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
