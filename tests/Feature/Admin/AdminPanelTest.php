<?php

namespace Tests\Feature\Admin;

use App\Models\AdminSetting;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Plan\PlanFeatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $workspace = Workspace::create([
            'name' => 'Admin Co', 'slug' => 'admin-co',
            'owner_email' => 'jootarisaac@gmail.com', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'agency',
            'trial_ends_at' => now()->addDays(30),
            'subscription_status' => 'active', 'language' => 'en',
        ]);

        return User::create([
            'workspace_id' => $workspace->id, 'name' => 'Isaac',
            'email' => 'jootarisaac@gmail.com', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
    }

    private function makeNonAdmin(): User
    {
        $workspace = Workspace::create([
            'name' => 'Regular Co', 'slug' => 'regular-co',
            'owner_email' => 'user@test.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(7),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);

        return User::create([
            'workspace_id' => $workspace->id, 'name' => 'User',
            'email' => 'user@test.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
    }

    // ── Access control ──────────────────────────────────────────────────────

    public function test_admin_dashboard_loads_for_admin(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/brandara-admin');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_admin_panel_blocks_non_admin(): void
    {
        $user = $this->makeNonAdmin();

        $response = $this->actingAs($user)->get('/brandara-admin');

        $response->assertStatus(403);
    }

    public function test_admin_panel_redirects_unauthenticated(): void
    {
        $response = $this->get('/brandara-admin');

        $response->assertRedirect('/login');
    }

    public function test_admin_workspaces_page_loads(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/brandara-admin/workspaces');

        $response->assertStatus(200);
        $response->assertSee('Workspaces');
    }

    public function test_admin_features_page_loads(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/brandara-admin/features');

        $response->assertStatus(200);
        $response->assertSee('Features');
    }

    public function test_admin_billing_page_loads(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/brandara-admin/billing');

        $response->assertStatus(200);
        $response->assertSee('Billing');
    }

    public function test_admin_ai_page_loads(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/brandara-admin/ai');

        $response->assertStatus(200);
        $response->assertSee('AI Settings');
    }

    // ── AdminSetting model ──────────────────────────────────────────────────

    public function test_admin_setting_get_and_set(): void
    {
        AdminSetting::set('test_key', 'test_value', 'general');

        $this->assertEquals('test_value', AdminSetting::get('test_key'));
    }

    public function test_admin_setting_get_returns_default(): void
    {
        $this->assertEquals('fallback', AdminSetting::get('nonexistent', 'fallback'));
    }

    public function test_admin_setting_json(): void
    {
        AdminSetting::setJson('test_json', ['a' => 1, 'b' => 2], 'general');

        $result = AdminSetting::getJson('test_json');
        $this->assertEquals(1, $result['a']);
        $this->assertEquals(2, $result['b']);
    }

    // ── PlanFeatureService reads from DB ─────────────────────────────────────

    public function test_plan_feature_service_reads_from_db(): void
    {
        AdminSetting::setJson('feature_gates', [
            'test_feature' => [
                'plans' => ['pro', 'agency'],
                'label' => 'Test',
                'description' => 'Test feature',
                'upgrade_to' => 'pro',
            ],
        ], 'features');

        $service = new PlanFeatureService;

        $this->assertFalse($service->planHas('starter', 'test_feature'));
        $this->assertTrue($service->planHas('pro', 'test_feature'));
        $this->assertTrue($service->planHas('agency', 'test_feature'));
    }

    public function test_plan_feature_service_reads_limits_from_db(): void
    {
        AdminSetting::setJson('generation_limits', ['starter' => 50, 'pro' => 0, 'agency' => 0], 'features');

        $service = new PlanFeatureService;

        $this->assertEquals(50, $service->generationLimit('starter'));
        $this->assertEquals(0, $service->generationLimit('pro'));
    }

    public function test_plan_feature_service_falls_back_to_config(): void
    {
        // No DB setting — should read from config
        $service = new PlanFeatureService;

        // config/features.php has ai_visibility gated to pro+agency
        $this->assertFalse($service->planHas('starter', 'ai_visibility'));
        $this->assertTrue($service->planHas('pro', 'ai_visibility'));
    }
}
