<?php

namespace Tests\Feature;

use App\Livewire\Analytics\ResultsDashboard;
use App\Models\Brand;
use App\Models\Post;
use App\Models\PostAnalytic;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Analytics\AnalyticsService;
use App\Services\Analytics\FakeAnalyticsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(string $plan = 'pro'): array
    {
        $workspace = Workspace::create([
            'name' => 'Analytics Co', 'slug' => 'analytics-co',
            'owner_email' => 'owner@analytics.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => $plan,
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@analytics.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Analytics Brand', 'slug' => 'analytics-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    private function makeAnalytic(Brand $brand, array $attrs = []): PostAnalytic
    {
        $post = Post::create([
            'brand_id'    => $brand->id,
            'created_by'  => Brand::find($brand->id)->workspace->users()->first()->id,
            'input_type'  => 'manual',
            'raw_input'   => 'Test post content',
            'status'      => 'published',
            'tone'        => 'professional',
            'platform_contents' => ['linkedin' => ['body' => 'test']],
        ]);

        return PostAnalytic::create(array_merge([
            'post_id'         => $post->id,
            'brand_id'        => $brand->id,
            'platform'        => 'linkedin',
            'fetched_date'    => now()->subDays(3)->toDateString(),
            'likes'           => 50,
            'comments'        => 10,
            'shares'          => 5,
            'reach'           => 1000,
            'clicks'          => 30,
            'saves'           => 8,
            'engagement_rate' => 6.5,
            'source'          => 'fake',
        ], $attrs));
    }

    // ── Route ─────────────────────────────────────────────────────────────────

    public function test_results_page_loads_for_growth_user(): void
    {
        [$user, $brand] = $this->makeWorkspace('pro');
        $this->actingAs($user);
        $this->get(route('results', ['brand' => $brand->slug]))->assertStatus(200);
    }

    public function test_results_page_shows_upgrade_prompt_for_basic(): void
    {
        [$user, $brand] = $this->makeWorkspace('starter');
        $this->actingAs($user);
        $this->get(route('results', ['brand' => $brand->slug]))
            ->assertStatus(200)
            ->assertSee('Upgrade to Growth');
    }

    // ── Component ─────────────────────────────────────────────────────────────

    public function test_component_mounts_with_no_data(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(ResultsDashboard::class, ['brand' => $brand])
            ->assertSee('No analytics data yet');
    }

    public function test_component_shows_dashboard_with_data(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->makeAnalytic($brand);

        Livewire::actingAs($user)
            ->test(ResultsDashboard::class, ['brand' => $brand])
            ->assertSee('Total reach')
            ->assertSee('Total engagements');
    }

    public function test_period_selector_changes_period(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->makeAnalytic($brand);

        Livewire::actingAs($user)
            ->test(ResultsDashboard::class, ['brand' => $brand])
            ->call('setPeriod', 7)
            ->assertSet('period', 7);
    }

    // ── AnalyticsService ──────────────────────────────────────────────────────

    public function test_summary_returns_correct_totals(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->makeAnalytic($brand, ['likes' => 100, 'comments' => 20, 'shares' => 10, 'reach' => 2000]);
        $this->makeAnalytic($brand, ['likes' => 50,  'comments' => 5,  'shares' => 3,  'reach' => 800, 'fetched_date' => now()->subDays(2)->toDateString(), 'platform' => 'twitter']);

        $svc     = app(AnalyticsService::class);
        $summary = $svc->summary($brand, 30);

        $this->assertEquals(2800, $summary['total_reach']);
        $this->assertEquals(188, $summary['total_engagements']); // 130 + 58
    }

    public function test_platform_breakdown_groups_correctly(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->makeAnalytic($brand, ['platform' => 'linkedin', 'likes' => 80, 'comments' => 10, 'shares' => 5]);
        $this->makeAnalytic($brand, ['platform' => 'twitter',  'likes' => 20, 'comments' => 3,  'shares' => 1, 'fetched_date' => now()->subDays(1)->toDateString()]);

        $breakdown = app(AnalyticsService::class)->platformBreakdown($brand, 30);

        $this->assertEquals(95, $breakdown->get('linkedin'));
        $this->assertEquals(24, $breakdown->get('twitter'));
    }

    // ── FakeAnalyticsSeeder ───────────────────────────────────────────────────

    public function test_fake_seeder_creates_records_for_published_posts(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Post::create([
            'brand_id' => $brand->id, 'created_by' => $user->id,
            'input_type' => 'manual', 'raw_input' => 'Post 1',
            'status' => 'published', 'tone' => 'professional',
            'platform_contents' => ['linkedin' => ['body' => 'test'], 'twitter' => ['body' => 'test']],
        ]);

        $count = app(FakeAnalyticsSeeder::class)->seed($brand, 30);

        $this->assertGreaterThan(0, $count);
        $this->assertDatabaseHas('post_analytics', ['brand_id' => $brand->id, 'source' => 'fake']);
    }
}
