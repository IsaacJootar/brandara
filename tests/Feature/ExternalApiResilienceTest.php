<?php

namespace Tests\Feature;

use App\Services\Billing\FlutterwaveProvider;
use App\Services\Billing\PaystackProvider;
use App\Support\ExternalApiRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalApiResilienceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    public function test_external_requests_have_bounded_connection_and_response_times(): void
    {
        $options = app(ExternalApiRequest::class)->make()->getOptions();

        $this->assertSame(5, $options['connect_timeout']);
        $this->assertSame(15, $options['timeout']);
    }

    public function test_external_requests_retry_transient_server_failure(): void
    {
        Http::fakeSequence()
            ->pushStatus(500)
            ->push(['ok' => true]);

        $response = app(ExternalApiRequest::class)->make()->get('https://api.example.test/status');

        $this->assertTrue($response->ok());
        Http::assertSentCount(2);
    }

    public function test_external_requests_do_not_retry_client_error(): void
    {
        Http::fakeSequence()
            ->pushStatus(422)
            ->push(['ok' => true]);

        $response = app(ExternalApiRequest::class)->make()->get('https://api.example.test/status');

        $this->assertSame(422, $response->status());
        Http::assertSentCount(1);
    }

    public function test_paystack_verification_recovers_from_transient_failure(): void
    {
        config()->set('services.paystack.secret_key', 'paystack-secret');
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::sequence()
                ->pushStatus(500)
                ->push([
                    'data' => [
                        'status' => 'success',
                        'currency' => 'NGN',
                        'amount' => 6000000,
                        'reference' => 'BRD-PS-123',
                        'customer' => ['id' => 'customer-1'],
                        'metadata' => [
                            'workspace_id' => 'workspace-1',
                            'plan' => 'pro',
                            'interval' => 'monthly',
                        ],
                    ],
                ]),
        ]);

        $result = app(PaystackProvider::class)->verifyPayment('BRD-PS-123');

        $this->assertSame('success', $result['status']);
        $this->assertSame(60000.0, $result['amount']);
        Http::assertSentCount(2);
    }

    public function test_flutterwave_connection_failure_returns_plain_failed_status(): void
    {
        config()->set('services.flutterwave.secret_key', 'flutterwave-secret');
        Http::fake([
            'api.flutterwave.com/*' => Http::sequence()
                ->pushFailedConnection()
                ->pushFailedConnection()
                ->pushFailedConnection(),
        ]);

        $result = app(FlutterwaveProvider::class)->verifyPayment('BRD-FW-123');

        $this->assertSame(['status' => 'failed'], $result);
        Http::assertSentCount(3);
    }
}
