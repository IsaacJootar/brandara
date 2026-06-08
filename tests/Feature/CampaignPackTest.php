<?php

namespace Tests\Feature;

use App\Livewire\Plan\Index;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Ai\AiProvider;
use App\Services\Ai\AiProviderFactory;
use App\Services\CampaignPack\CampaignPackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CampaignPackTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Acme', 'slug' => 'acme',
            'owner_email' => 'owner@acme.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@acme.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Acme Consulting', 'slug' => 'acme-consulting',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    private function mockProvider(string $json): AiProviderFactory
    {
        $mock = new class($json) implements AiProvider
        {
            public function __construct(private readonly string $json) {}

            public function name(): string
            {
                return 'Mock';
            }

            public function generate(string $sys, string $usr, int $max = 4096): string
            {
                return $this->json;
            }
        };

        $factory = $this->createMock(AiProviderFactory::class);
        $factory->method('make')->willReturn($mock);

        return $factory;
    }

    // ── Config ────────────────────────────────────────────────────────────────

    public function test_pack_config_has_expected_keys(): void
    {
        $packs = config('campaign-packs');
        $this->assertArrayHasKey('independence_day', $packs);
        $this->assertArrayHasKey('product_launch', $packs);
        $this->assertArrayHasKey('flash_sale', $packs);
    }

    public function test_each_pack_has_required_fields(): void
    {
        foreach (config('campaign-packs') as $key => $pack) {
            $this->assertArrayHasKey('name', $pack, "Pack {$key} missing name");
            $this->assertArrayHasKey('duration_days', $pack, "Pack {$key} missing duration_days");
            $this->assertArrayHasKey('default_goal', $pack, "Pack {$key} missing default_goal");
            $this->assertArrayHasKey('default_tone', $pack, "Pack {$key} missing default_tone");
        }
    }

    // ── CampaignPackService ───────────────────────────────────────────────────

    public function test_service_creates_posts_from_ai_response(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $campaign = Campaign::create([
            'brand_id' => $brand->id,
            'name' => 'Independence Day',
            'type' => 'pack',
            'pack_key' => 'independence_day',
            'goal' => 'brand awareness',
            'key_message' => 'We are proud to be Nigerian-built.',
            'start_date' => '2025-09-27',
            'end_date' => '2025-10-01',
            'platforms' => ['linkedin', 'twitter'],
            'tone' => 'african',
            'status' => 'draft',
        ]);

        $responseJson = json_encode([
            'campaign_summary' => 'A 5-day patriotic campaign',
            'whatsapp_broadcast' => 'Happy Independence Day! 🇳🇬',
            'posts' => [
                ['day' => 1, 'post_type' => 'awareness', 'angle' => 'Pride hook',
                    'platforms' => ['linkedin' => ['body' => 'Day 1 post', 'hashtags' => ['#NigeriaAt65']]]],
                ['day' => 2, 'post_type' => 'desire', 'angle' => 'Story',
                    'platforms' => ['linkedin' => ['body' => 'Day 2 post', 'hashtags' => ['#NigeriaAt65']]]],
                ['day' => 3, 'post_type' => 'action', 'angle' => 'CTA',
                    'platforms' => ['linkedin' => ['body' => 'Day 3 post', 'hashtags' => ['#NigeriaAt65']]]],
            ],
        ]);

        $pack = config('campaign-packs.independence_day');
        $service = new CampaignPackService($this->mockProvider($responseJson));
        $result = $service->generate($campaign, $brand, $pack);

        $this->assertSame('active', $result->status);
        $this->assertSame(3, Post::where('campaign_id', $campaign->id)->count());
    }

    public function test_service_handles_malformed_ai_response(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $campaign = Campaign::create([
            'brand_id' => $brand->id,
            'name' => 'Test Campaign',
            'type' => 'pack',
            'pack_key' => 'product_launch',
            'goal' => 'awareness',
            'key_message' => 'Launching now.',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(6)->format('Y-m-d'),
            'platforms' => ['linkedin'],
            'status' => 'draft',
        ]);

        $pack = config('campaign-packs.product_launch');
        $service = new CampaignPackService($this->mockProvider('not valid json'));
        $result = $service->generate($campaign, $brand, $pack);

        // Malformed response = 0 posts but campaign still marked active
        $this->assertSame('active', $result->status);
        $this->assertSame(0, Post::where('campaign_id', $campaign->id)->count());
    }

    // ── Plan component pack flow ───────────────────────────────────────────────

    public function test_plan_shows_pack_library_on_campaigns_tab(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('setTab', 'campaigns')
            ->assertSee('Campaign Packs')
            ->assertSee('Independence Day')
            ->assertSee('Product / Service Launch');
    }

    public function test_opening_pack_form_sets_active_pack(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openPackForm', 'product_launch')
            ->assertSet('activatingPackKey', 'product_launch')
            ->assertSet('packStatus', 'idle');
    }

    public function test_closing_pack_form_clears_state(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openPackForm', 'product_launch')
            ->call('closePackForm')
            ->assertSet('activatingPackKey', null);
    }

    public function test_generate_pack_validates_key_message(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openPackForm', 'product_launch')
            ->set('packKeyMessage', '')
            ->call('generatePack')
            ->assertHasErrors(['packKeyMessage']);
    }
}
