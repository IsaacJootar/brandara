<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'slug', 'owner_email', 'country', 'timezone',
        'plan', 'trial_ends_at', 'subscription_status',
        'paystack_customer_id', 'flutterwave_customer_id', 'language',
        'ai_generations_used', 'usage_reset_date',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'usage_reset_date' => 'date',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function isTrialing(): bool
    {
        return $this->subscription_status === 'trialing';
    }

    public function trialDaysLeft(): int
    {
        if (! $this->trial_ends_at) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
    }

    public function isActive(): bool
    {
        if ($this->subscription_status === 'active') {
            return true;
        }
        if ($this->subscription_status === 'trialing' && $this->trial_ends_at?->isFuture()) {
            return true;
        }

        return false;
    }
}
