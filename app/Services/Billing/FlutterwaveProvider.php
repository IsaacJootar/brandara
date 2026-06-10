<?php

namespace App\Services\Billing;

use App\Models\BillingPlan;
use App\Models\Workspace;
use App\Services\Billing\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlutterwaveProvider implements PaymentProviderInterface
{
    private string $secretKey;

    private string $publicKey;

    private string $baseUrl = 'https://api.flutterwave.com/v3';

    public function __construct()
    {
        $this->secretKey = config('services.flutterwave.secret_key', '');
        $this->publicKey = config('services.flutterwave.public_key', '');
    }

    public function name(): string
    {
        return 'flutterwave';
    }

    public function initializePayment(Workspace $workspace, BillingPlan $plan): array
    {
        $txRef = 'BRD-FW-'.Str::upper(Str::random(12));

        return [
            'tx_ref' => $txRef,
            'amount' => (float) $plan->amount,
            'currency' => $plan->currency,
            'public_key' => $this->publicKey,
            'customer' => [
                'email' => $workspace->owner_email,
                'name' => $workspace->name,
            ],
            'meta' => [
                'workspace_id' => $workspace->id,
                'plan' => $plan->plan,
                'interval' => $plan->interval,
                'billing_plan_id' => $plan->id,
            ],
            'customizations' => [
                'title' => 'Brandara — '.$plan->planLabel().' Plan',
                'description' => ucfirst($plan->interval).' subscription',
                'logo' => url('/images/brandara-icon.svg'),
            ],
        ];
    }

    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transactions/{$reference}/verify");

            if (! $response->ok()) {
                Log::warning('Flutterwave verify failed', ['ref' => $reference, 'status' => $response->status()]);

                return ['status' => 'failed'];
            }

            $data = $response->json('data');

            if (($data['status'] ?? '') !== 'successful') {
                return ['status' => 'failed'];
            }

            $meta = collect($data['meta'] ?? []);

            return [
                'status' => 'success',
                'plan' => $meta->get('plan', 'starter'),
                'interval' => $meta->get('interval', 'monthly'),
                'currency' => $data['currency'] ?? 'USD',
                'amount' => (float) ($data['amount'] ?? 0),
                'reference' => $data['tx_ref'] ?? $reference,
                'customer_id' => (string) ($data['customer']['id'] ?? ''),
                'workspace_id' => $meta->get('workspace_id'),
            ];
        } catch (\Throwable $e) {
            Log::error('Flutterwave verify exception', ['error' => $e->getMessage()]);

            return ['status' => 'failed'];
        }
    }

    public function parseWebhook(string $payload, string $signature): ?array
    {
        // Verify Flutterwave webhook signature
        $expectedHash = hash_hmac('sha256', $payload, config('services.flutterwave.webhook_secret', ''));

        if (! hash_equals($expectedHash, $signature)) {
            Log::warning('Flutterwave webhook signature mismatch');

            return null;
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? '';

        // Map Flutterwave events to internal event names
        $actionableEvents = [
            'charge.completed' => 'payment_success',
            'subscription.cancelled' => 'subscription_cancelled',
        ];

        if (! isset($actionableEvents[$event])) {
            return null;
        }

        $txData = $data['data'] ?? [];
        $meta = collect($txData['meta'] ?? []);

        return [
            'event' => $actionableEvents[$event],
            'reference' => $txData['tx_ref'] ?? $txData['flw_ref'] ?? '',
            'workspace_id' => $meta->get('workspace_id'),
        ];
    }
}
