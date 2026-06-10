<?php

namespace App\Http\Controllers;

use App\Services\Billing\BillingService;
use App\Services\Billing\FlutterwaveProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FlutterwaveWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('verif-hash', '');
        $billing = app(BillingService::class);
        $provider = app(FlutterwaveProvider::class);

        $event = $provider->parseWebhook($payload, $signature);

        if (! $event) {
            return response('Invalid signature', 400);
        }

        match ($event['event']) {
            'payment_success' => $billing->handlePaymentSuccess(
                array_merge(
                    $provider->verifyPayment($event['reference']),
                    ['provider' => 'flutterwave']
                )
            ),
            'subscription_cancelled' => $event['workspace_id']
                ? $billing->handleCancellation($event['workspace_id'])
                : null,
            default => null,
        };

        return response('OK', 200);
    }
}
