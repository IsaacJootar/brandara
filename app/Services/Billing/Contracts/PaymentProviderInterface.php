<?php

namespace App\Services\Billing\Contracts;

use App\Models\BillingPlan;
use App\Models\Workspace;

interface PaymentProviderInterface
{
    /**
     * Initialize a one-time or subscription payment.
     * Returns provider-specific data needed to open the payment popup.
     *
     * @return array{tx_ref: string, amount: float, currency: string, public_key: string, meta: array}
     */
    public function initializePayment(Workspace $workspace, BillingPlan $plan): array;

    /**
     * Verify a completed payment by reference.
     *
     * @return array{status: string, plan: string, interval: string, currency: string, amount: float, reference: string, customer_id: string|null}
     */
    public function verifyPayment(string $reference): array;

    /**
     * Verify and parse an incoming webhook payload.
     * Returns null if signature invalid or event not actionable.
     *
     * @return array{event: string, reference: string, workspace_id: string|null}|null
     */
    public function parseWebhook(string $payload, string $signature): ?array;

    /** Provider name — 'flutterwave' or 'paystack'. */
    public function name(): string;
}
