<?php

namespace Database\Seeders;

use App\Models\BillingPlan;
use App\Models\BillingSetting;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    /**
     * Seeds default billing plans and settings.
     * Admin can override all values from the Admin Panel (Module 22).
     * Yearly = 10 months price (2 months free).
     */
    public function run(): void
    {
        // ── Plans ──────────────────────────────────────────────────────────────
        $plans = [
            // Basic — $19/month · $190/year
            ['plan' => 'starter', 'interval' => 'monthly', 'currency' => 'USD', 'amount' => 19.00],
            ['plan' => 'starter', 'interval' => 'yearly',  'currency' => 'USD', 'amount' => 190.00],
            ['plan' => 'starter', 'interval' => 'monthly', 'currency' => 'NGN', 'amount' => 30000.00],
            ['plan' => 'starter', 'interval' => 'yearly',  'currency' => 'NGN', 'amount' => 300000.00],
            ['plan' => 'starter', 'interval' => 'monthly', 'currency' => 'GBP', 'amount' => 15.00],
            ['plan' => 'starter', 'interval' => 'yearly',  'currency' => 'GBP', 'amount' => 150.00],
            ['plan' => 'starter', 'interval' => 'monthly', 'currency' => 'GHS', 'amount' => 290.00],
            ['plan' => 'starter', 'interval' => 'yearly',  'currency' => 'GHS', 'amount' => 2900.00],
            ['plan' => 'starter', 'interval' => 'monthly', 'currency' => 'KES', 'amount' => 2500.00],
            ['plan' => 'starter', 'interval' => 'yearly',  'currency' => 'KES', 'amount' => 25000.00],
            ['plan' => 'starter', 'interval' => 'monthly', 'currency' => 'ZAR', 'amount' => 350.00],
            ['plan' => 'starter', 'interval' => 'yearly',  'currency' => 'ZAR', 'amount' => 3500.00],

            // Growth — $39/month · $390/year
            ['plan' => 'pro', 'interval' => 'monthly', 'currency' => 'USD', 'amount' => 39.00],
            ['plan' => 'pro', 'interval' => 'yearly',  'currency' => 'USD', 'amount' => 390.00],
            ['plan' => 'pro', 'interval' => 'monthly', 'currency' => 'NGN', 'amount' => 60000.00],
            ['plan' => 'pro', 'interval' => 'yearly',  'currency' => 'NGN', 'amount' => 600000.00],
            ['plan' => 'pro', 'interval' => 'monthly', 'currency' => 'GBP', 'amount' => 31.00],
            ['plan' => 'pro', 'interval' => 'yearly',  'currency' => 'GBP', 'amount' => 310.00],
            ['plan' => 'pro', 'interval' => 'monthly', 'currency' => 'GHS', 'amount' => 590.00],
            ['plan' => 'pro', 'interval' => 'yearly',  'currency' => 'GHS', 'amount' => 5900.00],
            ['plan' => 'pro', 'interval' => 'monthly', 'currency' => 'KES', 'amount' => 5000.00],
            ['plan' => 'pro', 'interval' => 'yearly',  'currency' => 'KES', 'amount' => 50000.00],
            ['plan' => 'pro', 'interval' => 'monthly', 'currency' => 'ZAR', 'amount' => 720.00],
            ['plan' => 'pro', 'interval' => 'yearly',  'currency' => 'ZAR', 'amount' => 7200.00],

            // Agency — $89/month · $890/year
            ['plan' => 'agency', 'interval' => 'monthly', 'currency' => 'USD', 'amount' => 89.00],
            ['plan' => 'agency', 'interval' => 'yearly',  'currency' => 'USD', 'amount' => 890.00],
            ['plan' => 'agency', 'interval' => 'monthly', 'currency' => 'NGN', 'amount' => 140000.00],
            ['plan' => 'agency', 'interval' => 'yearly',  'currency' => 'NGN', 'amount' => 1400000.00],
            ['plan' => 'agency', 'interval' => 'monthly', 'currency' => 'GBP', 'amount' => 70.00],
            ['plan' => 'agency', 'interval' => 'yearly',  'currency' => 'GBP', 'amount' => 700.00],
            ['plan' => 'agency', 'interval' => 'monthly', 'currency' => 'GHS', 'amount' => 1350.00],
            ['plan' => 'agency', 'interval' => 'yearly',  'currency' => 'GHS', 'amount' => 13500.00],
            ['plan' => 'agency', 'interval' => 'monthly', 'currency' => 'KES', 'amount' => 11500.00],
            ['plan' => 'agency', 'interval' => 'yearly',  'currency' => 'KES', 'amount' => 115000.00],
            ['plan' => 'agency', 'interval' => 'monthly', 'currency' => 'ZAR', 'amount' => 1650.00],
            ['plan' => 'agency', 'interval' => 'yearly',  'currency' => 'ZAR', 'amount' => 16500.00],
        ];

        foreach ($plans as $plan) {
            BillingPlan::updateOrCreate(
                ['plan' => $plan['plan'], 'interval' => $plan['interval'], 'currency' => $plan['currency']],
                ['amount' => $plan['amount'], 'is_active' => true]
            );
        }

        // ── Settings ───────────────────────────────────────────────────────────
        $settings = [
            'default_provider'       => 'flutterwave',   // Admin can toggle in Module 22
            'fallback_provider'      => 'paystack',
            'test_mode'              => 'true',           // Switch to false in production
            'yearly_discount_label'  => '2 months free', // Shown on pricing cards
            'default_currency'       => 'USD',            // Fallback currency for billing page
            'supported_currencies'   => 'USD,NGN,GBP,GHS,KES,ZAR',
        ];

        foreach ($settings as $key => $value) {
            BillingSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
