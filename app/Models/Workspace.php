<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Workspace extends Tenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'owner_email',
        'country',
        'timezone',
        'plan',
        'trial_ends_at',
        'subscription_status',
        'paystack_customer_id',
        'flutterwave_customer_id',
        'language',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'owner_email',
            'country',
            'timezone',
            'plan',
            'trial_ends_at',
            'subscription_status',
            'paystack_customer_id',
            'flutterwave_customer_id',
            'language',
        ];
    }
}
