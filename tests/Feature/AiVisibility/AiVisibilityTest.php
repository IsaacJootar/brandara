<?php

namespace Tests\Feature\AiVisibility;

use App\Models\AiGeneratedAsset;
use App\Models\AiPresenceResult;
use App\Models\AiVisibilityCheck;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AiVisibility\AiPresenceService;
use App\Services\AiVisibility\CountryDirectoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspaceAndBrand(): array
    {
        $workspace = Workspace::create([
            'name' => 'Test Co', 'slug' => 'test-co',
            'owner_email' => 'owner@test.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(7),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@test.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Gano Africa', 'slug' => 'gano-africa', 'language' => 'en',
        ]);

        return [$user, $workspace, $brand];
    }

    // ── Route loads ──────────────────────────────────────────────────────────

    public function test_ai_presence_page_loads_for_authenticated_user(): void
    {
        [$user, $workspace, $brand] = $this->makeWorkspaceAndBrand();

        $response = $this->actingAs($user)->get("/{$brand->slug}/ai-presence");

        $response->assertStatus(200);
        $response->assertSee('AI Visibility');
    }

    public function test_ai_presence_page_redirects_unauthenticated(): void
    {
        $this->makeWorkspaceAndBrand();

        $response = $this->get('/gano-africa/ai-presence');

        $response->assertRedirect();
    }

    // ── Models ───────────────────────────────────────────────────────────────

    public function test_ai_visibility_check_readiness_label(): void
    {
        [$user, $workspace, $brand] = $this->makeWorkspaceAndBrand();

        $check = AiVisibilityCheck::create([
            'brand_id' => $brand->id,
            'website_url' => 'https://example.com',
            'results' => [],
            'manual_checks' => [],
            'score' => 85,
            'tier1_passed' => 12,
            'tier2_passed' => 3,
            'tier3_passed' => 2,
            'scanned_at' => now(),
        ]);

        $this->assertEquals('Strong', $check->readinessLabel());
    }

    public function test_ai_visibility_check_low_score_label(): void
    {
        [$user, $workspace, $brand] = $this->makeWorkspaceAndBrand();

        $check = AiVisibilityCheck::create([
            'brand_id' => $brand->id,
            'website_url' => 'https://example.com',
            'results' => [],
            'manual_checks' => [],
            'score' => 30,
            'tier1_passed' => 3,
            'tier2_passed' => 1,
            'tier3_passed' => 0,
            'scanned_at' => now(),
        ]);

        $this->assertEquals('Weak', $check->readinessLabel());
    }

    public function test_ai_generated_asset_type_label(): void
    {
        [$user, $workspace, $brand] = $this->makeWorkspaceAndBrand();

        $asset = AiGeneratedAsset::create([
            'brand_id' => $brand->id,
            'type' => 'json_ld',
            'content' => '{}',
            'status' => 'draft',
            'generated_at' => now(),
        ]);

        $this->assertEquals('JSON-LD Schema', $asset->typeLabel());
    }

    // ── Country Directory Service ────────────────────────────────────────────

    public function test_country_directory_returns_nigeria_entries(): void
    {
        $service = new CountryDirectoryService;
        $dirs = $service->forCountry('NG');

        $this->assertNotEmpty($dirs);
        $this->assertEquals('Business Directories', $dirs[0]['category']);
    }

    public function test_country_directory_returns_global_for_unknown(): void
    {
        $service = new CountryDirectoryService;
        $dirs = $service->forCountry('XX');

        $this->assertNotEmpty($dirs);
        $this->assertEquals('Professional Platforms', $dirs[0]['category']);
    }

    // ── AI Presence Service ──────────────────────────────────────────────────

    public function test_build_prompts_generates_six_prompts(): void
    {
        [$user, $workspace, $brand] = $this->makeWorkspaceAndBrand();

        $service = new AiPresenceService;
        $prompts = $service->buildPrompts($brand);

        $this->assertCount(6, $prompts);
        $this->assertArrayHasKey('text', $prompts[0]);
        $this->assertArrayHasKey('category', $prompts[0]);
    }

    public function test_active_providers_returns_empty_without_keys(): void
    {
        config(['services.anthropic.key' => null]);
        config(['services.openai.key' => null]);
        config(['services.gemini.key' => null]);

        $service = new AiPresenceService;
        $providers = $service->activeProviders();

        $this->assertEmpty($providers);
    }

    public function test_presence_summary_returns_no_data_when_empty(): void
    {
        [$user, $workspace, $brand] = $this->makeWorkspaceAndBrand();

        $service = new AiPresenceService;
        $summary = $service->presenceSummary($brand);

        $this->assertFalse($summary['has_data']);
    }

    public function test_presence_summary_calculates_score(): void
    {
        [$user, $workspace, $brand] = $this->makeWorkspaceAndBrand();

        AiPresenceResult::create([
            'brand_id' => $brand->id, 'provider' => 'claude',
            'prompt' => 'Test', 'prompt_category' => 'discovery',
            'appeared' => true, 'position' => 1, 'sentiment' => 'positive',
            'raw_response' => 'Gano Africa is great', 'competitors_mentioned' => [],
            'queried_at' => now(),
        ]);
        AiPresenceResult::create([
            'brand_id' => $brand->id, 'provider' => 'chatgpt',
            'prompt' => 'Test', 'prompt_category' => 'discovery',
            'appeared' => false, 'sentiment' => 'not_mentioned',
            'raw_response' => 'No mention', 'competitors_mentioned' => [],
            'queried_at' => now(),
        ]);

        $service = new AiPresenceService;
        $summary = $service->presenceSummary($brand);

        $this->assertTrue($summary['has_data']);
        $this->assertEquals(2, $summary['total']);
        $this->assertEquals(1, $summary['appeared']);
        $this->assertEquals(50, $summary['score']);
    }
}
