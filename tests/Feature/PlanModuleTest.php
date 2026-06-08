<?php

namespace Tests\Feature;

use App\Livewire\Plan\Index;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\ContentPillar;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlanModuleTest extends TestCase
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

    public function test_plan_page_loads(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $this->actingAs($user)
            ->get("/{$brand->slug}/plan")
            ->assertOk()
            ->assertSeeLivewire('plan.index');
    }

    public function test_can_create_content_pillar(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openPillarForm')
            ->set('pillarName', 'Thought Leadership')
            ->set('pillarGoal', 'authority')
            ->set('pillarColor', '#7C3AED')
            ->call('savePillar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('content_pillars', [
            'brand_id' => $brand->id,
            'name' => 'Thought Leadership',
            'goal' => 'authority',
        ]);
    }

    public function test_cannot_exceed_five_pillars(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        for ($i = 1; $i <= 5; $i++) {
            ContentPillar::create([
                'brand_id' => $brand->id,
                'name' => "Pillar {$i}",
                'goal' => 'authority',
                'color' => '#7C3AED',
                'sort_order' => $i,
            ]);
        }

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openPillarForm')
            ->set('pillarName', 'Sixth Pillar')
            ->set('pillarGoal', 'trust')
            ->set('pillarColor', '#7C3AED')
            ->call('savePillar')
            ->assertHasErrors(['pillarName']);

        $this->assertSame(5, ContentPillar::where('brand_id', $brand->id)->count());
    }

    public function test_can_create_campaign(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('setTab', 'campaigns')
            ->call('openCampaignForm')
            ->set('campaignName', 'Black Friday 2025')
            ->set('campaignGoal', 'Drive 50 leads')
            ->set('campaignKeyMessage', 'Get 30% off our services this Black Friday.')
            ->set('campaignStartDate', now()->addDays(10)->format('Y-m-d'))
            ->set('campaignEndDate', now()->addDays(17)->format('Y-m-d'))
            ->set('campaignPlatforms', ['linkedin', 'twitter'])
            ->call('saveCampaign')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('campaigns', [
            'brand_id' => $brand->id,
            'name' => 'Black Friday 2025',
        ]);
    }

    public function test_campaign_end_date_must_be_after_start(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('setTab', 'campaigns')
            ->call('openCampaignForm')
            ->set('campaignName', 'Bad Campaign')
            ->set('campaignGoal', 'Some goal')
            ->set('campaignKeyMessage', 'Some message')
            ->set('campaignStartDate', now()->addDays(10)->format('Y-m-d'))
            ->set('campaignEndDate', now()->addDays(5)->format('Y-m-d'))
            ->set('campaignPlatforms', ['linkedin'])
            ->call('saveCampaign')
            ->assertHasErrors(['campaignEndDate']);
    }

    public function test_can_archive_campaign(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $campaign = Campaign::create([
            'brand_id' => $brand->id,
            'name' => 'Test Campaign',
            'type' => 'custom',
            'goal' => 'Test',
            'key_message' => 'Test',
            'platforms' => ['linkedin'],
            'status' => 'active',
        ]);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('archiveCampaign', $campaign->id);

        $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'status' => 'archived']);
    }
}
