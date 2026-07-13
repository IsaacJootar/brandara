<?php

namespace Tests\Feature;

use App\Livewire\Plan\Index;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\ContentPillar;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Attributes\Locked;
use Livewire\Livewire;
use ReflectionProperty;
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

    public function test_editing_record_identifiers_are_locked(): void
    {
        foreach (['editingPillarId', 'editingCampaignId'] as $property) {
            $attributes = (new ReflectionProperty(Index::class, $property))
                ->getAttributes(Locked::class);

            $this->assertCount(1, $attributes);
        }
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

    public function test_can_update_a_pillar_within_the_current_brand(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $pillar = ContentPillar::create([
            'brand_id' => $brand->id,
            'name' => 'Old Name',
            'goal' => 'authority',
            'color' => '#7C3AED',
            'sort_order' => 1,
        ]);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openPillarForm', $pillar->id)
            ->set('pillarName', 'Client Results')
            ->call('savePillar')
            ->assertHasNoErrors();

        $this->assertSame('Client Results', $pillar->fresh()->name);
        $this->assertSame($brand->id, $pillar->fresh()->brand_id);
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

    public function test_can_update_a_campaign_within_the_current_brand(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $campaign = Campaign::create([
            'brand_id' => $brand->id,
            'name' => 'Old Campaign',
            'type' => 'custom',
            'goal' => 'Old goal',
            'key_message' => 'Old message',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'platforms' => ['linkedin'],
            'status' => 'draft',
        ]);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openCampaignForm', $campaign->id)
            ->set('campaignName', 'Updated Campaign')
            ->set('campaignGoal', 'Updated goal')
            ->set('campaignKeyMessage', 'Updated campaign message')
            ->call('saveCampaign')
            ->assertHasNoErrors();

        $this->assertSame('Updated Campaign', $campaign->fresh()->name);
        $this->assertSame($brand->id, $campaign->fresh()->brand_id);
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
