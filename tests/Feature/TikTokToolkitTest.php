<?php

namespace Tests\Feature;

use App\Livewire\Create\TikTokToolkit;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Ai\AiProvider;
use App\Services\Ai\AiProviderFactory;
use App\Services\TikTok\TikTokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TikTokToolkitTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'TestCo', 'slug' => 'testco',
            'owner_email' => 'owner@testco.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@testco.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'TestCo Brand', 'slug' => 'testco-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    // ── Route ─────────────────────────────────────────────────────────────────

    public function test_tiktok_page_loads_for_authenticated_user(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $response = $this->get(route('create.tiktok', ['brand' => $brand->slug]));

        $response->assertStatus(200);
        $response->assertSee('TikTok Toolkit');
    }

    public function test_tiktok_page_requires_auth(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $response = $this->get(route('create.tiktok', ['brand' => $brand->slug]));

        $response->assertRedirect();
    }

    // ── Livewire component ────────────────────────────────────────────────────

    public function test_component_mounts_with_brand_id(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(TikTokToolkit::class, ['brand' => $brand])
            ->assertSet('brandId', $brand->id)
            ->assertSet('status', 'idle');
    }

    public function test_generate_validates_topic_required(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(TikTokToolkit::class, ['brand' => $brand])
            ->set('topic', '')
            ->call('generate')
            ->assertHasErrors(['topic' => 'required']);
    }

    public function test_generate_validates_topic_min_length(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(TikTokToolkit::class, ['brand' => $brand])
            ->set('topic', 'Hi')
            ->call('generate')
            ->assertHasErrors(['topic' => 'min']);
    }

    public function test_generate_calls_service_and_shows_result(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $fakeResult = [
            'caption' => 'Test caption #TikTok',
            'hashtags' => ['#TikTok', '#Business'],
            'script' => [
                'hook_seconds_1_to_3' => 'Did you know?',
                'content_body' => 'Here is the body.',
                'cta_closing' => 'Follow for more.',
                'total_duration' => '45 seconds',
            ],
            'text_overlays' => [
                ['timing' => '0–3s', 'text' => 'Did you know?'],
            ],
            'bio_copy' => 'Nigerian business tips | Follow',
            'content_tips' => 'Film in portrait mode.',
        ];

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn(json_encode($fakeResult));
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        Livewire::actingAs($user)
            ->test(TikTokToolkit::class, ['brand' => $brand])
            ->set('topic', 'Growing your business in Nigeria without big budgets')
            ->call('generate')
            ->assertSet('status', 'done')
            ->assertSet('result.caption', 'Test caption #TikTok');
    }

    public function test_start_over_clears_state(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(TikTokToolkit::class, ['brand' => $brand])
            ->set('topic', 'Some topic here')
            ->set('status', 'done')
            ->call('startOver')
            ->assertSet('topic', '')
            ->assertSet('status', 'idle')
            ->assertSet('result', []);
    }

    // ── TikTokService parse ───────────────────────────────────────────────────

    public function test_service_parses_valid_json_from_provider(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $payload = [
            'caption' => 'Hook caption',
            'hashtags' => ['#One', '#Two'],
            'script' => [
                'hook_seconds_1_to_3' => 'Hook text',
                'content_body' => 'Body text',
                'cta_closing' => 'CTA text',
                'total_duration' => '50 seconds',
            ],
            'text_overlays' => [],
            'bio_copy' => 'Bio here',
            'content_tips' => 'Tip 1',
        ];

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn(json_encode($payload));
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        $result = app(TikTokService::class)->generate($brand, 'Test topic for the video', 'founder');

        $this->assertEquals('Hook caption', $result['caption']);
        $this->assertEquals(['#One', '#Two'], $result['hashtags']);
        $this->assertEquals('Hook text', $result['script']['hook_seconds_1_to_3']);
    }

    public function test_service_falls_back_gracefully_on_invalid_json(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn('not json at all');
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        $result = app(TikTokService::class)->generate($brand, 'Test topic for the video', 'bold');

        $this->assertEquals('not json at all', $result['caption']);
        $this->assertIsArray($result['hashtags']);
        $this->assertEmpty($result['hashtags']);
    }
}
