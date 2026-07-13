<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Platforms\PlatformConnectionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PlatformOAuthStateTest extends TestCase
{
    use RefreshDatabase;

    private PlatformConnectionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PlatformConnectionService::class);
        Http::preventStrayRequests();
    }

    public function test_valid_state_is_opaque_and_creates_connection_once(): void
    {
        [, $user, $brand] = $this->makeWorkspace('First');
        $this->actingAs($user);
        $state = $this->stateFrom($this->service->buildAuthUrl('linkedin', $brand));

        $this->assertSame(64, strlen($state));
        $this->assertStringNotContainsString($brand->id, $state);

        $this->fakeLinkedIn();

        $connection = $this->service->handleCallback('linkedin', 'valid-code', $state);

        $this->assertSame($brand->id, $connection->brand_id);
        $this->assertSame('linkedin-user', $connection->platform_user_id);
        $this->assertNull(session('oauth_states.'.hash('sha256', $state)));

        $this->assertStateRejected(fn () => $this->service->handleCallback('linkedin', 'replayed-code', $state));
        Http::assertSentCount(2);
    }

    public function test_connection_route_accepts_valid_one_time_state(): void
    {
        [, $user, $brand] = $this->makeWorkspace('Route');
        $authorizationResponse = $this->actingAs($user)->get(route('platform.connect', [
            'brand' => $brand->slug,
            'platform' => 'linkedin',
        ]));
        $authorizationResponse->assertRedirect();
        $state = $this->stateFrom($authorizationResponse->headers->get('Location'));
        $this->fakeLinkedIn();

        $this->get(route('platform.callback', [
            'platform' => 'linkedin',
            'code' => 'valid-code',
            'state' => $state,
        ]))
            ->assertRedirect(route('connections', ['brand' => $brand->slug]))
            ->assertSessionHas('success', 'Brand Owner connected successfully.');
    }

    public function test_provider_outage_does_not_store_token_and_shows_plain_failure(): void
    {
        [, $user, $brand] = $this->makeWorkspace('Outage');
        $authorizationResponse = $this->actingAs($user)->get(route('platform.connect', [
            'brand' => $brand->slug,
            'platform' => 'linkedin',
        ]));
        $state = $this->stateFrom($authorizationResponse->headers->get('Location'));
        Http::fake([
            'www.linkedin.com/oauth/v2/accessToken' => Http::response([
                'access_token' => 'access-token',
            ]),
            'api.linkedin.com/v2/userinfo' => Http::sequence()
                ->pushStatus(500)
                ->pushStatus(500)
                ->pushStatus(500),
        ]);

        $this->get(route('platform.callback', [
            'platform' => 'linkedin',
            'code' => 'valid-code',
            'state' => $state,
        ]))
            ->assertRedirect(route('connections', ['brand' => $brand->slug]))
            ->assertSessionHas('error', 'Connection failed. Please check your credentials and try again.');

        $this->assertDatabaseCount('platform_connections', 0);
        Http::assertSentCount(4);
    }

    public function test_altered_state_is_rejected_before_provider_request(): void
    {
        [, $user, $brand] = $this->makeWorkspace('Altered');
        $this->actingAs($user);
        $state = $this->stateFrom($this->service->buildAuthUrl('linkedin', $brand));

        $this->assertStateRejected(
            fn () => $this->service->handleCallback('linkedin', 'code', $state.'changed'),
        );

        Http::assertNothingSent();
    }

    public function test_state_cannot_be_used_for_another_platform(): void
    {
        [, $user, $brand] = $this->makeWorkspace('Platform');
        $this->actingAs($user);
        $state = $this->stateFrom($this->service->buildAuthUrl('linkedin', $brand));

        $this->assertStateRejected(
            fn () => $this->service->handleCallback('facebook', 'code', $state),
        );

        Http::assertNothingSent();
    }

    public function test_expired_state_is_rejected(): void
    {
        [, $user, $brand] = $this->makeWorkspace('Expired');
        $this->actingAs($user);
        $state = $this->stateFrom($this->service->buildAuthUrl('linkedin', $brand));
        $this->travel(11)->minutes();

        $this->assertStateRejected(
            fn () => $this->service->handleCallback('linkedin', 'code', $state),
            'OAuth state expired. Please try connecting again.',
        );

        Http::assertNothingSent();
    }

    public function test_state_cannot_connect_a_brand_from_another_workspace(): void
    {
        [, $owner, $brand] = $this->makeWorkspace('Owner');
        [, $otherUser] = $this->makeWorkspace('Other');
        $this->actingAs($owner);
        $state = $this->stateFrom($this->service->buildAuthUrl('linkedin', $brand));
        $this->actingAs($otherUser);

        try {
            $this->service->handleCallback('linkedin', 'code', $state);
            $this->fail('OAuth state accepted for another workspace.');
        } catch (ModelNotFoundException) {
            $this->assertTrue(true);
        }

        Http::assertNothingSent();
    }

    public function test_cancelled_callback_consumes_state(): void
    {
        [, $user, $brand] = $this->makeWorkspace('Cancelled');
        $this->actingAs($user);
        $state = $this->stateFrom($this->service->buildAuthUrl('twitter', $brand));

        $resolvedBrand = $this->service->handleCancelledCallback('twitter', $state);

        $this->assertSame($brand->id, $resolvedBrand->id);
        $this->assertStateRejected(
            fn () => $this->service->handleCallback('twitter', 'code', $state),
        );
        Http::assertNothingSent();
    }

    public function test_twitter_pkce_verifier_is_bound_to_pending_state(): void
    {
        [, $user, $brand] = $this->makeWorkspace('Twitter');
        $this->actingAs($user);
        $state = $this->stateFrom($this->service->buildAuthUrl('twitter', $brand));
        Http::fake([
            'api.twitter.com/2/oauth2/token' => Http::response([
                'access_token' => 'twitter-access-token',
                'refresh_token' => 'twitter-refresh-token',
                'expires_in' => 7200,
            ]),
            'api.twitter.com/2/users/me' => Http::response([
                'data' => ['id' => 'twitter-user', 'username' => 'brandowner'],
            ]),
        ]);

        $connection = $this->service->handleCallback('twitter', 'twitter-code', $state);

        $this->assertSame('twitter-user', $connection->platform_user_id);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.twitter.com/2/oauth2/token'
                && is_string($request['code_verifier'])
                && strlen($request['code_verifier']) === 64;
        });
    }

    private function fakeLinkedIn(): void
    {
        Http::fake([
            'www.linkedin.com/oauth/v2/accessToken' => Http::response([
                'access_token' => 'access-token',
                'refresh_token' => 'refresh-token',
                'expires_in' => 3600,
            ]),
            'api.linkedin.com/v2/userinfo' => Http::response([
                'sub' => 'linkedin-user',
                'name' => 'Brand Owner',
            ]),
        ]);
    }

    private function stateFrom(string $authorizationUrl): string
    {
        parse_str((string) parse_url($authorizationUrl, PHP_URL_QUERY), $query);

        $this->assertArrayHasKey('state', $query);

        return $query['state'];
    }

    private function assertStateRejected(callable $callback, string $message = 'Invalid OAuth state.'): void
    {
        try {
            $callback();
            $this->fail('Invalid OAuth state was accepted.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertSame($message, $exception->getMessage());
        }
    }

    /**
     * @return array{Workspace, User, Brand}
     */
    private function makeWorkspace(string $name): array
    {
        $workspace = Workspace::factory()->create([
            'name' => "{$name} Workspace",
            'slug' => strtolower($name).'-workspace',
        ]);
        $user = User::factory()->for($workspace)->create([
            'email' => strtolower($name).'@oauth.test',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => "{$name} Brand",
            'slug' => strtolower($name).'-brand',
            'language' => 'en',
        ]);

        return [$workspace, $user, $brand];
    }
}
