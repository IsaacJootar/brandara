<?php

namespace Tests\Feature\AiVisibility;

use App\Livewire\AiVisibility\Dashboard;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AiVisibility\WebsiteScannerService;
use App\Support\PublicUrlGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Livewire\Livewire;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class WebsiteScannerSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_scanner_rejects_private_addresses_before_sending_requests(): void
    {
        [, , $brand] = $this->makeWorkspace();
        Http::fake();

        try {
            app(WebsiteScannerService::class)->scan($brand, 'http://127.0.0.1/admin');
            $this->fail('Private website address was accepted.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Website address must point to a public website.', $exception->getMessage());
        }

        Http::assertNothingSent();
        $this->assertDatabaseCount('ai_visibility_checks', 0);
    }

    public function test_scanner_does_not_follow_a_redirect_to_a_private_address(): void
    {
        [, , $brand] = $this->makeWorkspace();
        $guard = Mockery::mock(PublicUrlGuard::class);
        $guard->shouldReceive('allows')->andReturnUsing(
            fn (string $url): bool => ! str_contains($url, '127.0.0.1'),
        );
        $service = new WebsiteScannerService($guard);

        Http::fake(function (Request $request) {
            if ($request->url() === 'https://public.example') {
                return Http::response('', 302, ['Location' => 'http://127.0.0.1/private']);
            }

            return Http::response('', 404);
        });

        $service->scan($brand, 'https://public.example');

        Http::assertNotSent(
            fn (Request $request): bool => str_contains($request->url(), '127.0.0.1'),
        );
    }

    public function test_scanner_rejects_a_private_sitemap_from_robots_file(): void
    {
        [, , $brand] = $this->makeWorkspace();
        $guard = Mockery::mock(PublicUrlGuard::class);
        $guard->shouldReceive('allows')->andReturnUsing(
            fn (string $url): bool => ! str_contains($url, '169.254.169.254'),
        );
        $service = new WebsiteScannerService($guard);

        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/robots.txt')) {
                return Http::response("User-agent: *\nSitemap: http://169.254.169.254/latest/meta-data", 200);
            }

            return Http::response('<html><title>Public site</title></html>', 200);
        });

        $check = $service->scan($brand, 'https://public.example');

        $this->assertSame('fail', $check->results['has_xml_sitemap']);
        Http::assertNotSent(
            fn (Request $request): bool => str_contains($request->url(), '169.254.169.254'),
        );
    }

    public function test_failed_scan_does_not_save_rejected_url_to_brand(): void
    {
        [, $user, $brand] = $this->makeWorkspace();
        $brand->update(['website_url' => 'https://safe.example']);

        $scanner = Mockery::mock(WebsiteScannerService::class);
        $scanner->shouldReceive('checkDefinitions')->andReturn([]);
        $scanner->shouldReceive('scan')->once()->andThrow(new RuntimeException('Rejected'));
        $this->app->instance(WebsiteScannerService::class, $scanner);

        Livewire::actingAs($user)
            ->test(Dashboard::class, ['brand' => $brand])
            ->set('websiteUrl', 'http://127.0.0.1/private')
            ->call('runScan')
            ->assertSet('errorMessage', 'Could not scan the website. Make sure the URL is correct and the site is live.');

        $this->assertSame('https://safe.example', $brand->fresh()->website_url);
    }

    /**
     * @return array{Workspace, User, Brand}
     */
    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Scanner Co',
            'slug' => 'scanner-co',
            'owner_email' => 'owner@scanner.test',
            'country' => 'NG',
            'timezone' => 'Africa/Lagos',
            'plan' => 'pro',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing',
            'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id,
            'name' => 'Owner',
            'email' => 'owner@scanner.test',
            'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Scanner Brand',
            'slug' => 'scanner-brand',
            'language' => 'en',
        ]);

        return [$workspace, $user, $brand];
    }
}
