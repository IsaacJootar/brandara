<?php

namespace Tests\Feature;

use App\Livewire\Grow\EngagementAutomation;
use App\Models\Brand;
use App\Models\EngagementAction;
use App\Models\EngagementRule;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Engagement\EngagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EngagementAutomationTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Grow Co', 'slug' => 'grow-co',
            'owner_email' => 'owner@grow.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'pro',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@grow.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Grow Brand', 'slug' => 'grow-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    public function test_grow_page_loads_for_pro_user(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);
        $this->get(route('grow', ['brand' => $brand->slug]))->assertStatus(200);
    }

    public function test_component_mounts(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(EngagementAutomation::class, ['brand' => $brand])
            ->assertSet('brandId', $brand->id)
            ->assertSet('showForm', false);
    }

    public function test_open_form_shows_form(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(EngagementAutomation::class, ['brand' => $brand])
            ->call('openForm')
            ->assertSet('showForm', true);
    }

    public function test_save_rule_requires_accounts_or_keywords(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(EngagementAutomation::class, ['brand' => $brand])
            ->call('openForm')
            ->set('accountsRaw', '')
            ->set('keywordsRaw', '')
            ->call('saveRule')
            ->assertSet('formError', 'Add at least one account handle or keyword to target.');
    }

    public function test_save_rule_validates_daily_limit_max(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(EngagementAutomation::class, ['brand' => $brand])
            ->call('openForm')
            ->set('dailyLimit', 999)
            ->set('accountsRaw', '@testuser')
            ->call('saveRule')
            ->assertHasErrors(['dailyLimit']);
    }

    public function test_save_auto_like_rule_creates_record(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(EngagementAutomation::class, ['brand' => $brand])
            ->call('openForm')
            ->set('ruleType', 'auto_like')
            ->set('platform', 'linkedin')
            ->set('accountsRaw', '@johndoe, @janebusiness')
            ->set('keywordsRaw', 'branding, Lagos')
            ->set('dailyLimit', 25)
            ->call('saveRule')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('engagement_rules', [
            'brand_id' => $brand->id,
            'type' => 'auto_like',
            'platform' => 'linkedin',
            'daily_limit' => 25,
            'is_active' => 1,
        ]);
    }

    public function test_save_auto_comment_rule_sets_review_and_tone(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(EngagementAutomation::class, ['brand' => $brand])
            ->call('openForm')
            ->set('ruleType', 'auto_comment')
            ->set('platform', 'linkedin')
            ->set('accountsRaw', '@expert')
            ->set('requireReview', true)
            ->set('commentTone', 'founder')
            ->call('saveRule');

        $rule = EngagementRule::where('brand_id', $brand->id)->first();
        $this->assertTrue($rule->require_review);
        $this->assertEquals('founder', $rule->comment_tone);
    }

    public function test_toggle_rule_flips_active_state(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $rule = EngagementRule::create([
            'brand_id' => $brand->id, 'type' => 'auto_like',
            'platform' => 'linkedin', 'daily_limit' => 20,
            'is_active' => true, 'require_review' => false,
        ]);
        Livewire::actingAs($user)
            ->test(EngagementAutomation::class, ['brand' => $brand])
            ->call('toggleRule', $rule->id);
        $this->assertFalse($rule->fresh()->is_active);
    }

    public function test_delete_rule_removes_record(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $rule = EngagementRule::create([
            'brand_id' => $brand->id, 'type' => 'auto_like',
            'platform' => 'linkedin', 'daily_limit' => 20,
            'is_active' => true, 'require_review' => false,
        ]);
        Livewire::actingAs($user)
            ->test(EngagementAutomation::class, ['brand' => $brand])
            ->call('deleteRule', $rule->id);
        $this->assertDatabaseMissing('engagement_rules', ['id' => $rule->id]);
    }

    public function test_approve_comment_action_marks_posted(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $rule = EngagementRule::create([
            'brand_id' => $brand->id, 'type' => 'auto_comment',
            'platform' => 'linkedin', 'daily_limit' => 20,
            'is_active' => true, 'require_review' => true,
        ]);
        $action = EngagementAction::create([
            'brand_id' => $brand->id, 'rule_id' => $rule->id,
            'type' => 'comment', 'platform' => 'linkedin',
            'target_account' => 'johndoe',
            'comment_body' => 'Thoughtful insight on this topic.',
            'status' => 'pending',
        ]);
        app(EngagementService::class)->approveComment($action);
        $this->assertEquals('posted', $action->fresh()->status);
    }

    public function test_skip_action_marks_skipped(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $rule = EngagementRule::create([
            'brand_id' => $brand->id, 'type' => 'auto_comment',
            'platform' => 'linkedin', 'daily_limit' => 20,
            'is_active' => true, 'require_review' => true,
        ]);
        $action = EngagementAction::create([
            'brand_id' => $brand->id, 'rule_id' => $rule->id,
            'type' => 'comment', 'platform' => 'linkedin',
            'target_account' => 'johndoe',
            'comment_body' => 'A comment', 'status' => 'pending',
        ]);
        app(EngagementService::class)->skipAction($action);
        $this->assertEquals('skipped', $action->fresh()->status);
    }

    public function test_daily_limit_resets_on_new_day(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $rule = EngagementRule::create([
            'brand_id' => $brand->id, 'type' => 'auto_like',
            'platform' => 'linkedin', 'daily_limit' => 5,
            'is_active' => true, 'require_review' => false,
            'actions_today' => 5,
            'actions_reset_date' => now()->subDay()->toDateString(),
        ]);
        $this->assertFalse($rule->isDailyLimitReached());
    }

    public function test_daily_limit_reached_today(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $rule = EngagementRule::create([
            'brand_id' => $brand->id, 'type' => 'auto_like',
            'platform' => 'linkedin', 'daily_limit' => 5,
            'is_active' => true, 'require_review' => false,
            'actions_today' => 5,
            'actions_reset_date' => now()->toDateString(),
        ]);
        $this->assertTrue($rule->isDailyLimitReached());
    }
}
