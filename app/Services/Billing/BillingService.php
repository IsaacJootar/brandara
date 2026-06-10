<?php

namespace App\Services\Billing;

use App\Mail\PaymentReceiptMail;
use App\Models\BillingPlan;
use App\Models\BillingSetting;
use App\Models\Subscription;
use App\Models\Workspace;
use App\Services\Billing\Contracts\PaymentProviderInterface;
use App\Services\Plan\PlanFeatureService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BillingService
{
    /**
     * Resolve the active payment provider from billing_settings.
     * Default: Flutterwave. Fallback: Paystack.
     * Admin toggles this in Module 22 — no code change needed.
     */
    public function provider(): PaymentProviderInterface
    {
        $name = BillingSetting::get('default_provider', 'flutterwave');

        return match ($name) {
            'paystack' => app(PaystackProvider::class),
            default => app(FlutterwaveProvider::class),
        };
    }

    /**
     * Get plans for the billing page.
     * Returns plans grouped by interval for the selected currency.
     *
     * @return array{monthly: Collection, yearly: Collection}
     */
    public function plansForCurrency(string $currency): array
    {
        $plans = BillingPlan::active()
            ->where('currency', $currency)
            ->orderByRaw("CASE plan WHEN 'starter' THEN 1 WHEN 'pro' THEN 2 WHEN 'agency' THEN 3 END")
            ->get();

        return [
            'monthly' => $plans->where('interval', 'monthly')->values(),
            'yearly' => $plans->where('interval', 'yearly')->values(),
        ];
    }

    /**
     * Detect the best default currency based on workspace country.
     */
    public function currencyForWorkspace(Workspace $workspace): string
    {
        return match ($workspace->country) {
            'NG' => 'NGN',
            'GH' => 'GHS',
            'KE' => 'KES',
            'ZA' => 'ZAR',
            'GB' => 'GBP',
            default => BillingSetting::get('default_currency', 'USD'),
        };
    }

    /**
     * Initialize a payment — returns provider data for the frontend popup.
     */
    public function initializePayment(Workspace $workspace, string $planId): array
    {
        $plan = BillingPlan::findOrFail($planId);

        return $this->provider()->initializePayment($workspace, $plan);
    }

    /**
     * Handle a successful payment (called after webhook or callback verification).
     * Updates workspace plan, creates/updates subscription record.
     */
    public function handlePaymentSuccess(array $paymentData): bool
    {
        try {
            $workspace = Workspace::find($paymentData['workspace_id'] ?? null);

            if (! $workspace) {
                Log::warning('Billing: workspace not found', $paymentData);

                return false;
            }

            // Upgrade workspace plan
            $workspace->update([
                'plan' => $paymentData['plan'],
                'subscription_status' => 'active',
            ]);

            // Record subscription
            Subscription::updateOrCreate(
                ['workspace_id' => $workspace->id],
                [
                    'plan' => $paymentData['plan'],
                    'interval' => $paymentData['interval'],
                    'currency' => $paymentData['currency'],
                    'amount' => $paymentData['amount'],
                    'status' => 'active',
                    'provider' => $paymentData['provider'],
                    'provider_reference' => $paymentData['reference'],
                    'provider_customer_id' => $paymentData['customer_id'] ?? null,
                    'current_period_start' => now(),
                    'current_period_end' => $paymentData['interval'] === 'yearly'
                        ? now()->addYear()
                        : now()->addMonth(),
                ]
            );

            // Send receipt email
            try {
                Mail::to($workspace->owner_email)
                    ->send(new PaymentReceiptMail($workspace, $paymentData));
            } catch (\Throwable $e) {
                Log::warning('Receipt email failed', ['error' => $e->getMessage()]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Billing handlePaymentSuccess failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Handle subscription cancellation.
     */
    public function handleCancellation(string $workspaceId): void
    {
        $workspace = Workspace::find($workspaceId);

        if (! $workspace) {
            return;
        }

        Subscription::where('workspace_id', $workspaceId)->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $workspace->update(['subscription_status' => 'cancelled']);
    }

    /**
     * Get the current active subscription for a workspace.
     */
    public function currentSubscription(Workspace $workspace): ?Subscription
    {
        return Subscription::where('workspace_id', $workspace->id)
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    /**
     * Usage summary for the billing page.
     *
     * @return array{brands_used: int, brands_limit: int|string, generations_used: int, generations_limit: int|string}
     */
    public function usageSummary(Workspace $workspace): array
    {
        $planSvc = app(PlanFeatureService::class);

        return [
            'brands_used' => $workspace->brands()->count(),
            'brands_limit' => $planSvc->brandLimit($workspace->plan),
            'generations_used' => $workspace->ai_generations_used ?? 0,
            'generations_limit' => $planSvc->generationLimit($workspace->plan),
        ];
    }
}
