<?php

namespace App\Services\Billing;

use App\Models\BillingPlan;
use App\Models\Workspace;
use App\Services\Billing\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackProvider implements PaymentProviderInterface
{
    private string $secretKey;

    private string $publicKey;

    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key', '');
        $this->publicKey = config('services.paystack.public_key', '');
    }

    public function name(): string
    {
        return 'paystack';
    }

    public function initializePayment(Workspace $workspace, BillingPlan $plan): array
    {
        $txRef = 'BRD-PS-'.Str::upper(Str::random(12));
        $amountInMinorUnit = (int) ((float) $plan->amount * 100);

        return [
            'tx_ref' => $txRef,
            'amount' => $amountInMinorUnit,
            'currency' => $plan->currency,
            'public_key' => $this->publicKey,
            'email' => $workspace->owner_email,
            'metadata' => [
                'workspace_id' => $workspace->id,
                'plan' => $plan->plan,
                'interval' => $plan->interval,
                'billing_plan_id' => $plan->id,
            ],
        ];
    }

    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transaction/verify/{$reference}");

            if (! $response->ok()) {
                return ['status' => 'failed'];
            }

            $data = $response->json('data');

            if (($data['status'] ?? '') !== 'success') {
                return ['status' => 'failed'];
            }

            $meta = collect($data['metadata'] ?? []);

            return [
                'status' => 'success',
                'plan' => $meta->get('plan', 'starter'),
                'interval' => $meta->get('interval', 'monthly'),
                'currency' => $data['currency'] ?? 'NGN',
                'amount' => (float) ($data['amount'] ?? 0) / 100,
                'reference' => $data['reference'] ?? $reference,
                'customer_id' => (string) ($data['customer']['id'] ?? ''),
                'workspace_id' => $meta->get('workspace_id'),
            ];
        } catch (\Throwable $e) {
            Log::error('Paystack verify exception', ['error' => $e->getMessage()]);

            return ['status' => 'failed'];
        }
    }

    public function parseWebhook(string $payload, string $signature): ?array
    {
        $expectedHash = hash_hmac('sha512', $payload, config('services.paystack.secret_key', ''));

        if (! hash_equals($expectedHash, $signature)) {
            Log::warning('Paystack webhook signature mismatch');

            return null;
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? '';

        $actionableEvents = [
            'charge.success' => 'payment_success',
            'subscription.disable' => 'subscription_cancelled',
            'subscription.not_renew' => 'subscription_cancelled',
        ];

        if (! isset($actionableEvents[$event])) {
            return null;
        }

        $txData = $data['data'] ?? [];
        $meta = collect($txData['metadata'] ?? []);

        return [
            'event' => $actionableEvents[$event],
            'reference' => $txData['reference'] ?? '',
            'workspace_id' => $meta->get('workspace_id'),
        ];
    }
}
