<?php

namespace Tests\Feature;

use App\Livewire\PostComposer;
use App\Models\Brand;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_create_page_loads(): void
    {
        [, $user, $brand] = $this->makeWorkspaceUserBrand();

        $this->actingAs($user)
            ->get("/{$brand->slug}/create")
            ->assertOk()
            ->assertSeeLivewire(PostComposer::class);
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
}
