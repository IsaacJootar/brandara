<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Platform-level key-value settings managed from /brandara-admin.
 * Groups: general, billing, ai, features
 */
class AdminSetting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'group'];

    /**
     * Get a setting value with fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        return $setting?->value ?? $default;
    }

    /**
     * Set a setting value (upsert).
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * Get all settings in a group as key => value array.
     */
    public static function group(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }

    /**
     * Get a JSON setting decoded as array.
     */
    public static function getJson(string $key, array $default = []): array
    {
        $value = static::get($key);

        if (! $value) {
            return $default;
        }

        return json_decode($value, true) ?: $default;
    }

    /**
     * Set a JSON setting.
     */
    public static function setJson(string $key, array $value, string $group = 'general'): void
    {
        static::set($key, json_encode($value), $group);
    }
}
