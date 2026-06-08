<?php

namespace Tests\Feature;

use App\Livewire\MyBrand\BrandKit;
use App\Livewire\MyBrand\BrandProfile;
use App\Livewire\MyBrand\CompletionScore;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BrandProfileModuleTest extends TestCase
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

    // ── My Brand page ─────────────────────────────────────────────────────────

    public function test_my_brand_page_loads_with_all_tabs(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $response = $this->get("/{$brand->slug}/my-brand");
        $response->assertStatus(200);
        $response->assertSee('Brand Kit');
        $response->assertSee('Brand Profile');
        $response->assertSee('Brand Voice');
    }

    // ── Brand Kit ─────────────────────────────────────────────────────────────

    public function test_brand_kit_loads_existing_values(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $brand->update(['tagline' => 'We help founders scale', 'primary_color' => '#FF0000']);
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandKit::class)
            ->assertSet('tagline', 'We help founders scale')
            ->assertSet('primaryColor', '#FF0000');
    }

    public function test_brand_kit_saves_to_database(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandKit::class)
            ->set('tagline', 'Africa first, always')
            ->set('description', 'We help SMEs grow faster.')
            ->set('targetAudience', 'Nigerian founders aged 30–50')
            ->set('primaryColor', '#7C3AED')
            ->call('save')
            ->assertSet('saveStatus', 'saved');

        $brand->refresh();
        $this->assertSame('Africa first, always', $brand->tagline);
        $this->assertSame('We help SMEs grow faster.', $brand->description);
        $this->assertSame('#7C3AED', $brand->primary_color);
    }

    public function test_brand_kit_requires_name(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandKit::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    // ── Brand Profile ─────────────────────────────────────────────────────────

    public function test_brand_profile_saves_all_fields(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandProfile::class)
            ->set('vision', 'Lead West Africa in advisory')
            ->set('mission', 'Give founders fair financial guidance')
            ->set('negativeBrief', 'Never use corporate jargon')
            ->set('positioning', 'Only firm focused on African SMEs')
            ->call('save')
            ->assertSet('saveStatus', 'saved');

        $brand->refresh();
        $this->assertSame('Lead West Africa in advisory', $brand->vision);
        $this->assertSame('Never use corporate jargon', $brand->negative_brief);
    }

    public function test_brand_profile_saves_values_as_array(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandProfile::class)
            ->set('values', [
                ['title' => 'Integrity', 'description' => 'We do what we say'],
                ['title' => 'Clarity', 'description' => 'No jargon, ever'],
            ])
            ->call('save')
            ->assertSet('saveStatus', 'saved');

        $brand->refresh();
        $this->assertCount(2, $brand->values);
        $this->assertSame('Integrity', $brand->values[0]['title']);
    }

    public function test_brand_profile_strips_empty_value_rows(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandProfile::class)
            ->set('values', [
                ['title' => 'Integrity', 'description' => 'We do what we say'],
                ['title' => '', 'description' => ''],
            ])
            ->call('save');

        $brand->refresh();
        $this->assertCount(1, $brand->values);
    }

    public function test_add_value_capped_at_five(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        $component = Livewire::withoutLazyLoading()->test(BrandProfile::class);

        // Add until 5
        $component->call('addValue')->call('addValue')->call('addValue')->call('addValue');
        $this->assertCount(5, $component->get('values'));

        // Should not exceed 5
        $component->call('addValue');
        $this->assertCount(5, $component->get('values'));
    }

    // ── Completion score ──────────────────────────────────────────────────────

    public function test_completion_score_low_for_mostly_empty_brand(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        // Only name is set (from makeWorkspace), everything else empty
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        $score = Livewire::withoutLazyLoading()
            ->test(CompletionScore::class)
            ->instance()
            ->percentage();

        $this->assertLessThan(20, $score);
    }

    public function test_completion_score_100_when_all_fields_filled(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $brand->update([
            'tagline' => 'We build Africa',
            'description' => 'Advisory firm',
            'target_audience' => 'Founders',
            'primary_color' => '#7C3AED',
            'vision' => 'Top firm',
            'mission' => 'Help founders',
            'negative_brief' => 'No jargon',
            'positioning' => 'Only Africa-focused',
            'values' => [['title' => 'Integrity', 'description' => 'We do what we say']],
            'brand_voice' => ['writing_summary' => 'Direct and warm'],
        ]);
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        $score = Livewire::withoutLazyLoading()
            ->test(CompletionScore::class)
            ->instance()
            ->percentage();

        $this->assertSame(100, $score);
    }
}
