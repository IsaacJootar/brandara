<?php

namespace Tests\Feature;

use App\Livewire\Grow\LeadTracker;
use App\Models\Brand;
use App\Models\Lead;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadTrackerTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Lead Co', 'slug' => 'lead-co',
            'owner_email' => 'owner@lead.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'pro',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@lead.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Lead Brand', 'slug' => 'lead-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    private function makeLead(Brand $brand, array $attrs = []): Lead
    {
        return Lead::create(array_merge([
            'brand_id'          => $brand->id,
            'platform'          => 'linkedin',
            'platform_user_id'  => 'user-'.uniqid(),
            'name'              => 'Amara Osei',
            'headline'          => 'CEO at Osei Consulting',
            'company'           => 'Osei Consulting',
            'total_engagements' => 3,
            'last_engaged_at'   => now(),
        ], $attrs));
    }

    // ── Route ─────────────────────────────────────────────────────────────────

    public function test_grow_page_loads(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);
        $this->get(route('grow', ['brand' => $brand->slug]))->assertStatus(200);
    }

    // ── Component ─────────────────────────────────────────────────────────────

    public function test_component_mounts(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->assertSet('brandId', $brand->id);
    }

    public function test_leads_display_in_list(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $lead = $this->makeLead($brand);

        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->assertSee($lead->name);
    }

    public function test_search_filters_by_name(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->makeLead($brand, ['name' => 'Kwame Mensah']);
        $this->makeLead($brand, ['name' => 'Fatima Diallo', 'platform_user_id' => 'u2']);

        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->set('search', 'Kwame')
            ->assertSee('Kwame Mensah')
            ->assertDontSee('Fatima Diallo');
    }

    public function test_filter_by_tag(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->makeLead($brand, ['name' => 'Hot Lead', 'tag' => 'warm_lead']);
        $this->makeLead($brand, ['name' => 'Cold Lead', 'tag' => 'prospect', 'platform_user_id' => 'u2']);

        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->set('filterTag', 'warm_lead')
            ->assertSee('Hot Lead')
            ->assertDontSee('Cold Lead');
    }

    public function test_start_edit_loads_lead_data(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $lead = $this->makeLead($brand, ['tag' => 'client', 'notes' => 'Met at summit']);

        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->call('startEdit', $lead->id)
            ->assertSet('editingId', $lead->id)
            ->assertSet('editTag', 'client')
            ->assertSet('editNotes', 'Met at summit');
    }

    public function test_save_edit_updates_lead(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $lead = $this->makeLead($brand);

        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->call('startEdit', $lead->id)
            ->set('editTag', 'warm_lead')
            ->set('editNotes', 'Very interested in our Growth package')
            ->set('editFollowUp', now()->addDays(3)->format('Y-m-d'))
            ->call('saveEdit')
            ->assertSet('editingId', null);

        $this->assertEquals('warm_lead', $lead->fresh()->tag);
        $this->assertEquals('Very interested in our Growth package', $lead->fresh()->notes);
    }

    public function test_delete_lead_removes_record(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $lead = $this->makeLead($brand);

        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->call('deleteLead', $lead->id);

        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_cancel_edit_clears_state(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $lead = $this->makeLead($brand, ['tag' => 'prospect']);

        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->call('startEdit', $lead->id)
            ->call('cancelEdit')
            ->assertSet('editingId', null)
            ->assertSet('editTag', '');
    }

    public function test_stats_count_correctly(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->makeLead($brand, ['tag' => 'warm_lead']);
        $this->makeLead($brand, ['tag' => 'warm_lead', 'platform_user_id' => 'u2']);
        $this->makeLead($brand, ['tag' => 'prospect',  'platform_user_id' => 'u3']);

        $this->assertEquals(3, Lead::where('brand_id', $brand->id)->count());
        $this->assertEquals(2, Lead::where('brand_id', $brand->id)->where('tag', 'warm_lead')->count());
    }

    public function test_cannot_edit_lead_from_another_brand(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $otherWorkspace = Workspace::create([
            'name' => 'Other', 'slug' => 'other-ws',
            'owner_email' => 'o@other.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'pro',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $otherBrand = Brand::create([
            'workspace_id' => $otherWorkspace->id,
            'name' => 'Other Brand', 'slug' => 'other-brand', 'language' => 'en',
        ]);
        $otherLead = $this->makeLead($otherBrand);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($user)
            ->test(LeadTracker::class, ['brand' => $brand])
            ->call('startEdit', $otherLead->id);
    }
}
