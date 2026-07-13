<?php

namespace Tests\Feature;

use App\Livewire\PostComposer;
use App\Models\Brand;
use App\Models\ContentPillar;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Attributes\Locked;
use Livewire\Livewire;
use ReflectionProperty;
use Tests\TestCase;

class PostComposerTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspaceUserBrand(): array
    {
        $workspace = Workspace::create([
            'name' => 'Acme',
            'slug' => 'acme',
            'owner_email' => 'owner@acme.test',
            'country' => 'NG',
            'timezone' => 'Africa/Lagos',
            'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing',
            'language' => 'en',
        ]);

        $user = User::create([
            'workspace_id' => $workspace->id,
            'name' => 'Owner',
            'email' => 'owner@acme.test',
            'password' => bcrypt('secret-pass'),
            'role' => 'owner',
        ]);

        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Acme Consulting',
            'slug' => 'acme-consulting',
            'language' => 'en',
        ]);

        return [$workspace, $user, $brand];
    }

    public function test_saved_draft_identifier_is_locked(): void
    {
        $attributes = (new ReflectionProperty(PostComposer::class, 'savedDraftId'))
            ->getAttributes(Locked::class);

        $this->assertCount(1, $attributes);
    }

    public function test_create_page_loads(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        $this->actingAs($user)
            ->get("/{$brand->slug}/create")
            ->assertOk()
            ->assertSeeLivewire(PostComposer::class);
    }

    public function test_create_page_includes_mobile_layout_constraints(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        $this->actingAs($user)
            ->get("/{$brand->slug}/create")
            ->assertOk()
            ->assertSee('create-composer-shell', false)
            ->assertSee('composer-input-tabs', false)
            ->assertSee('composer-actions', false)
            ->assertSee('brandara-topbar-name', false);

        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('@media (max-width: 640px)', $css);
        $this->assertStringContainsString('.create-composer-shell', $css);
    }

    public function test_save_draft_persists_post_scoped_to_brand(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        $this->actingAs($user);

        Livewire::test(PostComposer::class, ['brand' => $brand])
            ->set('body', 'Hello world from Brandara test.')
            ->set('platforms', ['linkedin'])
            ->set('tone', 'professional')
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertSet('saveStatus', 'saved');

        $this->assertDatabaseHas('posts', [
            'brand_id' => $brand->id,
            'status' => 'draft',
            'tone' => 'professional',
        ]);
    }

    public function test_save_draft_requires_body(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        $this->actingAs($user);

        Livewire::test(PostComposer::class, ['brand' => $brand])
            ->set('body', '')
            ->call('saveDraft')
            ->assertHasErrors(['body' => 'required']);

        $this->assertSame(0, Post::count());
    }

    public function test_character_count_treats_unicode_characters_as_one_each(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        $component = Livewire::actingAs($user)
            ->test(PostComposer::class, ['brand' => $brand])
            ->set('body', 'Café 😊');

        $this->assertSame(6, $component->instance()->charCount());
    }

    public function test_save_draft_accepts_unicode_text_at_platform_limit(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        Livewire::actingAs($user)
            ->test(PostComposer::class, ['brand' => $brand])
            ->set('platforms', ['twitter'])
            ->set('body', str_repeat('é', 280))
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertSet('saveStatus', 'saved');

        $this->assertSame(1, Post::count());
    }

    public function test_save_draft_rejects_text_over_selected_platform_limit(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        Livewire::actingAs($user)
            ->test(PostComposer::class, ['brand' => $brand])
            ->set('platforms', ['linkedin', 'twitter'])
            ->set('body', str_repeat('é', 281))
            ->call('saveDraft')
            ->assertHasErrors(['body'])
            ->assertSee('Too long for: X. Shorten your post or deselect those platforms.');

        $this->assertSame(0, Post::count());
    }

    public function test_save_draft_rejects_unknown_platform(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        Livewire::actingAs($user)
            ->test(PostComposer::class, ['brand' => $brand])
            ->set('platforms', ['unknown-platform'])
            ->set('body', 'Valid post body')
            ->call('saveDraft')
            ->assertHasErrors(['platforms.0']);

        $this->assertSame(0, Post::count());
    }

    public function test_save_draft_updates_only_the_loaded_brand_draft(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();
        $this->actingAs($user);

        $post = Post::create([
            'brand_id' => $brand->id,
            'created_by' => $user->id,
            'input_type' => 'manual',
            'raw_input' => 'Original draft',
            'platform_contents' => ['linkedin' => ['body' => 'Original draft']],
            'status' => 'draft',
        ]);

        Livewire::test(PostComposer::class, ['brand' => $brand])
            ->call('loadVariation', $post->id, 'Original draft')
            ->set('body', 'Updated draft')
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertSet('savedDraftId', $post->id);

        $this->assertSame('Updated draft', $post->fresh()->raw_input);
        $this->assertSame($brand->id, $post->fresh()->brand_id);
    }

    public function test_save_draft_rejects_a_pillar_from_another_brand(): void
    {
        [$workspace, $user, $brand] = $this->makeWorkspaceUserBrand();
        $this->actingAs($user);

        $otherBrand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Other Brand',
            'slug' => 'other-brand',
            'language' => 'en',
        ]);
        $otherPillar = ContentPillar::create([
            'brand_id' => $otherBrand->id,
            'name' => 'Other Pillar',
            'goal' => 'authority',
            'color' => '#7C3AED',
            'sort_order' => 1,
        ]);

        Livewire::test(PostComposer::class, ['brand' => $brand])
            ->set('body', 'A valid draft body')
            ->set('pillarId', $otherPillar->id)
            ->call('saveDraft')
            ->assertHasErrors(['pillarId' => 'exists']);

        $this->assertSame(0, Post::count());
    }

    public function test_toggle_platform_adds_and_removes(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        $this->actingAs($user);

        Livewire::test(PostComposer::class, ['brand' => $brand])
            ->set('platforms', ['linkedin'])
            ->call('togglePlatform', 'twitter')
            ->assertSet('platforms', ['linkedin', 'twitter'])
            ->call('togglePlatform', 'linkedin')
            ->assertSet('platforms', ['twitter']);
    }

    public function test_reactive_selectors_keep_the_clicked_values(): void
    {
        [$workspace, $user, $brand] = $this->makeWorkspaceUserBrand();
        $workspace->update(['plan' => 'pro']);

        Livewire::actingAs($user)
            ->test(PostComposer::class, ['brand' => $brand])
            ->call('setInputType', 'product')
            ->call('setTone', 'bold')
            ->call('togglePlatform', 'instagram')
            ->assertSet('inputType', 'product')
            ->assertSet('tone', 'bold')
            ->assertSet('platforms', ['linkedin', 'instagram'])
            ->assertSeeHtml('wire:key="composer-input-product"')
            ->assertSeeHtml('wire:key="composer-tone-bold"')
            ->assertSeeHtml('wire:key="composer-platform-instagram"');
    }
}
