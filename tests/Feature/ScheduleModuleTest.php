<?php

namespace Tests\Feature;

use App\Jobs\PublishPostJob;
use App\Livewire\Schedule\Index;
use App\Models\Brand;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class ScheduleModuleTest extends TestCase
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

    private function makePost(Brand $brand, array $attrs = []): Post
    {
        return Post::create(array_merge([
            'brand_id' => $brand->id,
            'created_by' => $brand->workspace->users()->first()->id,
            'input_type' => 'manual',
            'raw_input' => 'Test post content.',
            'ai_generated' => false,
            'platform_contents' => ['linkedin' => ['body' => 'Test post content.']],
            'tone' => 'professional',
            'status' => 'draft',
        ], $attrs));
    }

    // ── Page loads ────────────────────────────────────────────────────────────

    public function test_schedule_page_loads(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $this->actingAs($user)
            ->get("/{$brand->slug}/schedule")
            ->assertOk()
            ->assertSeeLivewire('schedule.index');
    }

    // ── Schedule a draft ──────────────────────────────────────────────────────

    public function test_draft_can_be_scheduled(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $post = $this->makePost($brand);

        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openSchedule', $post->id)
            ->set('scheduleDate', now()->addDay()->format('Y-m-d'))
            ->set('scheduleTime', '09:00')
            ->call('confirmSchedule')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_cannot_schedule_in_the_past(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $post = $this->makePost($brand);

        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('openSchedule', $post->id)
            ->set('scheduleDate', now()->subDay()->format('Y-m-d'))
            ->set('scheduleTime', '09:00')
            ->call('confirmSchedule')
            ->assertHasErrors(['scheduleDate']);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'status' => 'draft']);
    }

    // ── Cancel schedule ───────────────────────────────────────────────────────

    public function test_scheduled_post_can_be_cancelled_back_to_draft(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $post = $this->makePost($brand, [
            'status' => 'scheduled',
            'scheduled_at' => now()->addHour(),
        ]);

        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('cancelSchedule', $post->id);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'status' => 'draft']);
    }

    // ── Retry failed ──────────────────────────────────────────────────────────

    public function test_failed_post_can_be_retried(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $post = $this->makePost($brand, [
            'status' => 'failed',
            'failure_reason' => 'Network timeout.',
        ]);

        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Index::class, ['brand' => $brand])
            ->call('retryFailed', $post->id);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'status' => 'scheduled']);
    }

    // ── Dispatch command ──────────────────────────────────────────────────────

    public function test_dispatch_command_queues_due_posts(): void
    {
        Queue::fake();
        [$user, $brand] = $this->makeWorkspace();

        // Due now
        $this->makePost($brand, ['status' => 'scheduled', 'scheduled_at' => now()->subMinute()]);
        // Future — should NOT be dispatched
        $this->makePost($brand, ['status' => 'scheduled', 'scheduled_at' => now()->addHour()]);

        $this->artisan('posts:dispatch-due')->assertSuccessful();

        Queue::assertPushed(PublishPostJob::class, 1);
    }

    // ── Publish job (fake publisher) ──────────────────────────────────────────

    public function test_publish_job_marks_post_published(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        // Add a connected platform so the job can publish
        $brand->platformConnections()->create([
            'platform' => 'linkedin',
            'platform_user_id' => 'fake-uid',
            'access_token' => encrypt('fake-token'),
            'status' => 'connected',
        ]);

        $post = $this->makePost($brand, [
            'status' => 'scheduled',
            'scheduled_at' => now()->subMinute(),
        ]);

        PublishPostJob::dispatchSync($post->id);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'status' => 'published']);
    }
}
