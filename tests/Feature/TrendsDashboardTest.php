<?php

namespace Tests\Feature;

use App\Livewire\Trends\TrendsDashboard;
use App\Models\Brand;
use App\Models\TrendSignal;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Trends\FakeTrendsSeeder;
use App\Services\Trends\TrendsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TrendsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(string $plan = 'pro'): array
    {
        $workspace = Workspace::create([
            'name' => 'Trends Co', 'slug' => 'trends-co',
            'owner_email' => 'owner@trends.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => $plan,
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@trends.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Trends Brand', 'slug' => 'trends-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    // ── Route ─────────────────────────────────────────────────────────────────

    public function test_trends_page_loads_for_growth_user(): void
    {
        [$user, $brand] = $this->makeWorkspace('pro');
        $this->actingAs($user);
        $this->get(route('trends', ['brand' => $brand->slug]))->assertStatus(200);
    }

    // ── Component — no data ───────────────────────────────────────────────────

    public function test_component_shows_no_data_state(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(TrendsDashboard::class, ['brand' => $brand])
            ->assertSee('Your trend data is on its way');
    }

    // ── FakeTrendsSeeder ──────────────────────────────────────────────────────

    public function test_fake_seeder_creates_all_three_categories(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $count = app(FakeTrendsSeeder::class)->seed($brand);

        $this->assertEquals(30, $count);
        $this->assertDatabaseHas('trend_signals', ['brand_id' => $brand->id, 'category' => 'industry']);
        $this->assertDatabaseHas('trend_signals', ['brand_id' => $brand->id, 'category' => 'format']);
        $this->assertDatabaseHas('trend_signals', ['brand_id' => $brand->id, 'category' => 'competitor']);
    }

    public function test_fake_seeder_clears_old_fake_data_on_reseed(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        app(FakeTrendsSeeder::class)->seed($brand);
        app(FakeTrendsSeeder::class)->seed($brand);

        $this->assertEquals(30, TrendSignal::where('brand_id', $brand->id)->count());
    }

    // ── TrendsService ─────────────────────────────────────────────────────────

    public function test_summary_returns_correct_counts(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        app(FakeTrendsSeeder::class)->seed($brand);

        $summary = app(TrendsService::class)->summary($brand);

        $this->assertEquals(10, $summary['industry_count']);
        $this->assertEquals(10, $summary['format_count']);
        $this->assertEquals(10, $summary['competitor_count']);
        $this->assertNotEmpty($summary['hot_platform']);
    }

    public function test_industry_trends_ordered_by_strength(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        app(FakeTrendsSeeder::class)->seed($brand);

        $trends = app(TrendsService::class)->industryTrends($brand);

        $this->assertGreaterThanOrEqual(
            $trends->last()->strength,
            $trends->first()->strength
        );
    }

    // ── Livewire component with data ──────────────────────────────────────────

    public function test_component_shows_dashboard_with_data(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        app(FakeTrendsSeeder::class)->seed($brand);

        Livewire::actingAs($user)
            ->test(TrendsDashboard::class, ['brand' => $brand])
            ->assertSee('Industry signals')
            ->assertSee('Industry Trends');
    }

    public function test_tab_switching_works(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        app(FakeTrendsSeeder::class)->seed($brand);

        Livewire::actingAs($user)
            ->test(TrendsDashboard::class, ['brand' => $brand])
            ->call('setTab', 'format')
            ->assertSet('activeTab', 'format')
            ->call('setTab', 'competitor')
            ->assertSet('activeTab', 'competitor');
    }

    // ── Tracked keywords ──────────────────────────────────────────────────────

    public function test_can_add_tracked_keyword(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        app(FakeTrendsSeeder::class)->seed($brand);

        Livewire::actingAs($user)
            ->test(TrendsDashboard::class, ['brand' => $brand])
            ->set('newKeyword', '#PersonalBranding')
            ->call('addKeyword');

        $this->assertDatabaseHas('tracked_keywords', [
            'brand_id' => $brand->id,
            'keyword' => '#personalbranding',
        ]);
    }

    public function test_can_remove_tracked_keyword(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        app(FakeTrendsSeeder::class)->seed($brand);

        $kw = app(TrendsService::class)->addKeyword($brand, '#TestKeyword');

        Livewire::actingAs($user)
            ->test(TrendsDashboard::class, ['brand' => $brand])
            ->call('removeKeyword', $kw->id);

        $this->assertDatabaseMissing('tracked_keywords', ['id' => $kw->id]);
    }
}
