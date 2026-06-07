<?php

namespace Tests\Feature;

use App\Livewire\Schedule\Index;
use App\Models\Brand;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function setup_brand(): array
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
            'email' => 'owner@acme.test', 'password' => bcrypt('secret-pass'),
            'role' => 'owner',
        ]);

        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Acme', 'slug' => 'acme', 'language' => 'en',
        ]);

        return [$user, $brand];
    }

    private function draft(Brand $brand, User $user): Post
    {
        return Post::create([
            'brand_id' => $brand->id,
            'created_by' => $user->id,
            'input_type' => 'manual',
            'raw_input' => 'Draft body',
            'platform_contents' => ['linkedin' => ['body' => 'Draft body']],
            'tone' => 'professional',
            'status' => 'draft',
        ]);
    }

    public function test_schedule_page_loads_with_lazy_index(): void
    {
        [$user, $brand] = $this->setup_brand();

        $this->actingAs($user)
            ->get("/{$brand->slug}/schedule")
            ->assertOk()
            ->assertSeeLivewire('schedule.index');
    }

    public function test_confirm_schedule_moves_draft_to_scheduled(): void
    {
        [$user, $brand] = $this->setup_brand();
        $this->actingAs($user);
        $post = $this->draft($brand, $user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openSchedule', $post->id)
            ->set('scheduleDate', now()->addDay()->format('Y-m-d'))
            ->set('scheduleTime', '10:30')
            ->call('confirmSchedule')
            ->assertHasNoErrors();

        $post->refresh();
        $this->assertSame('scheduled', $post->status);
        $this->assertNotNull($post->scheduled_at);
        $this->assertTrue($post->scheduled_at->isFuture());
    }

    public function test_past_schedule_time_is_rejected(): void
    {
        [$user, $brand] = $this->setup_brand();
        $this->actingAs($user);
        $post = $this->draft($brand, $user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openSchedule', $post->id)
            ->set('scheduleDate', now()->subDay()->format('Y-m-d'))
            ->set('scheduleTime', '09:00')
            ->call('confirmSchedule')
            ->assertHasErrors('scheduleDate');

        $this->assertSame('draft', $post->fresh()->status);
    }

    public function test_cancel_schedule_moves_back_to_draft(): void
    {
        [$user, $brand] = $this->setup_brand();
        $this->actingAs($user);

        $post = Post::create([
            'brand_id' => $brand->id, 'created_by' => $user->id,
            'input_type' => 'manual', 'raw_input' => 'x',
            'platform_contents' => ['linkedin' => ['body' => 'x']],
            'tone' => 'professional',
            'status' => 'scheduled', 'scheduled_at' => now()->addDay(),
        ]);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('cancelSchedule', $post->id);

        $post->refresh();
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->scheduled_at);
    }

    public function test_retry_failed_requeues_post(): void
    {
        [$user, $brand] = $this->setup_brand();
        $this->actingAs($user);

        $post = Post::create([
            'brand_id' => $brand->id, 'created_by' => $user->id,
            'input_type' => 'manual', 'raw_input' => 'x',
            'platform_contents' => ['linkedin' => ['body' => 'x']],
            'tone' => 'professional',
            'status' => 'failed', 'failure_reason' => 'something went wrong',
            'retry_count' => 3,
        ]);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('retryFailed', $post->id);

        $post->refresh();
        $this->assertSame('scheduled', $post->status);
        $this->assertNull($post->failure_reason);
        $this->assertSame(0, $post->retry_count);
    }

    public function test_other_workspace_brand_url_is_forbidden(): void
    {
        // Defence-in-depth: ResolveBrand middleware blocks the URL before
        // the Livewire component ever mounts.
        [$user] = $this->setup_brand();
        $this->actingAs($user);

        $otherWorkspace = Workspace::create([
            'name' => 'Other', 'slug' => 'other',
            'owner_email' => 'o@o.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $otherBrand = Brand::create([
            'workspace_id' => $otherWorkspace->id,
            'name' => 'Other', 'slug' => 'other-brand', 'language' => 'en',
        ]);

        $this->get("/{$otherBrand->slug}/schedule")->assertForbidden();
    }
}
