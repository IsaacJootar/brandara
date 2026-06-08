<?php

namespace Tests\Feature;

use App\Livewire\Create\PillarAlert;
use App\Livewire\PostComposer;
use App\Models\Brand;
use App\Models\ContentPillar;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContentPillarsAdvancedTest extends TestCase
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

    // ── PostComposer pillar selector ──────────────────────────────────────────

    public function test_composer_shows_pillar_selector_when_pillars_exist(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        ContentPillar::create([
            'brand_id' => $brand->id,
            'name' => 'Thought Leadership',
            'goal' => 'authority',
            'color' => '#7C3AED',
            'is_active' => true,
        ]);
        $this->actingAs($user);

        Livewire::test(PostComposer::class, ['brand' => $brand])
            ->assertSee('Thought Leadership');
    }

    public function test_composer_saves_pillar_id_with_draft(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $pillar = ContentPillar::create([
            'brand_id' => $brand->id,
            'name' => 'Client Wins',
            'goal' => 'trust',
            'color' => '#0369A1',
            'is_active' => true,
        ]);
        $this->actingAs($user);

        Livewire::test(PostComposer::class, ['brand' => $brand])
            ->set('body', 'We just helped a client double their revenue.')
            ->set('pillarId', $pillar->id)
            ->call('saveDraft');

        $post = Post::where('brand_id', $brand->id)->first();
        $this->assertSame($pillar->id, $post->content_pillar_id);
    }

    public function test_composer_saves_null_pillar_when_none_selected(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::test(PostComposer::class, ['brand' => $brand])
            ->set('body', 'A post without a pillar.')
            ->set('pillarId', null)
            ->call('saveDraft');

        $post = Post::where('brand_id', $brand->id)->first();
        $this->assertNull($post->content_pillar_id);
    }

    // ── Pillar alert ──────────────────────────────────────────────────────────

    public function test_alert_hidden_for_brand_with_no_posts(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        ContentPillar::create([
            'brand_id' => $brand->id,
            'name' => 'Personal Story',
            'goal' => 'trust',
            'color' => '#BE123C',
            'is_active' => true,
        ]);
        $this->actingAs($user);

        $neglected = Livewire::test(PillarAlert::class, ['brand' => $brand])
            ->instance()
            ->neglectedPillars();

        $this->assertCount(0, $neglected);
    }

    public function test_alert_shows_pillar_not_used_in_14_days(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $pillar = ContentPillar::create([
            'brand_id' => $brand->id,
            'name' => 'Personal Story',
            'goal' => 'trust',
            'color' => '#BE123C',
            'is_active' => true,
        ]);

        // Brand has posts but none tagged to this pillar
        Post::create([
            'brand_id' => $brand->id,
            'created_by' => $user->id,
            'status' => 'published',
            'input_type' => 'manual',
            'raw_input' => 'A published post.',
            'platform_contents' => ['linkedin' => ['body' => 'test']],
            'tone' => 'professional',
            'published_at' => now()->subDays(20),
        ]);

        $this->actingAs($user);

        $neglected = Livewire::test(PillarAlert::class, ['brand' => $brand])
            ->instance()
            ->neglectedPillars();

        $this->assertCount(1, $neglected);
        $this->assertSame($pillar->id, $neglected[0]['pillar']->id);
    }

    public function test_alert_not_shown_for_recently_used_pillar(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $pillar = ContentPillar::create([
            'brand_id' => $brand->id,
            'name' => 'Thought Leadership',
            'goal' => 'authority',
            'color' => '#7C3AED',
            'is_active' => true,
        ]);

        Post::create([
            'brand_id' => $brand->id,
            'content_pillar_id' => $pillar->id,
            'created_by' => $user->id,
            'status' => 'published',
            'input_type' => 'manual',
            'raw_input' => 'Recent post on this pillar.',
            'platform_contents' => ['linkedin' => ['body' => 'test']],
            'tone' => 'professional',
            'published_at' => now()->subDays(3),
        ]);

        $this->actingAs($user);

        $neglected = Livewire::test(PillarAlert::class, ['brand' => $brand])
            ->instance()
            ->neglectedPillars();

        $this->assertCount(0, $neglected);
    }

    public function test_alert_can_be_dismissed(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::test(PillarAlert::class, ['brand' => $brand])
            ->call('dismiss')
            ->assertSet('dismissed', true);
    }
}
