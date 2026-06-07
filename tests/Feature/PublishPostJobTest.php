<?php

namespace Tests\Feature;

use App\Jobs\PublishPostJob;
use App\Models\Brand;
use App\Models\PlatformConnection;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Platforms\Publishers\PlatformPublisher;
use App\Services\Platforms\Publishers\PublisherFactory;
use App\Services\Platforms\Publishers\PublishResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PublishPostJobTest extends TestCase
{
    use RefreshDatabase;

    private function scenario(): array
    {
        $workspace = Workspace::create([
            'name' => 'Acme', 'slug' => 'acme',
            'owner_email' => 'o@o.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'O',
            'email' => 'o@o.test', 'password' => bcrypt('secret-pass'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Acme', 'slug' => 'acme', 'language' => 'en',
        ]);

        $connection = PlatformConnection::create([
            'brand_id' => $brand->id,
            'platform' => 'linkedin',
            'platform_user_id' => 'u123',
            'access_token' => 'token',
            'status' => 'connected',
        ]);

        $post = Post::create([
            'brand_id' => $brand->id,
            'created_by' => $user->id,
            'input_type' => 'manual',
            'raw_input' => 'Hello world',
            'platform_contents' => ['linkedin' => ['body' => 'Hello world']],
            'tone' => 'professional',
            'status' => 'scheduled',
            'scheduled_at' => now()->subMinute(),
        ]);

        return [$brand, $connection, $post];
    }

    public function test_successful_publish_marks_post_published_with_live_url(): void
    {
        [, , $post] = $this->scenario();

        (new PublishPostJob($post->id))->handle(new PublisherFactory);

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertArrayHasKey('linkedin', $post->live_post_urls);
    }

    public function test_no_connected_platforms_marks_failed_with_reason(): void
    {
        [$brand, $connection, $post] = $this->scenario();
        $connection->update(['status' => 'expired']);

        (new PublishPostJob($post->id))->handle(new PublisherFactory);

        $post->refresh();
        $this->assertSame('failed', $post->status);
        $this->assertStringContainsString('Reconnect', $post->failure_reason);
    }

    public function test_token_expired_failure_does_not_retry(): void
    {
        [, , $post] = $this->scenario();

        $factory = $this->mock(PublisherFactory::class);
        $publisher = $this->mock(PlatformPublisher::class);
        $publisher->shouldReceive('publish')
            ->andReturn(PublishResult::fail('token_expired', 'LinkedIn token expired.'));
        $factory->shouldReceive('for')->andReturn($publisher);

        (new PublishPostJob($post->id))->handle($factory);

        $post->refresh();
        $this->assertSame('failed', $post->status);
        $this->assertStringContainsString('token expired', strtolower($post->failure_reason));
    }

    public function test_dispatch_due_command_queues_due_posts_only(): void
    {
        [$brand, , $duePost] = $this->scenario();

        // A future post that should NOT be dispatched
        Post::create([
            'brand_id' => $brand->id,
            'created_by' => $duePost->created_by,
            'input_type' => 'manual',
            'raw_input' => 'Future',
            'platform_contents' => ['linkedin' => ['body' => 'Future']],
            'tone' => 'professional',
            'status' => 'scheduled',
            'scheduled_at' => now()->addDay(),
        ]);

        Queue::fake();

        $this->artisan('posts:dispatch-due')->assertExitCode(0);

        Queue::assertPushed(PublishPostJob::class, 1);
        Queue::assertPushed(PublishPostJob::class, fn ($job) => $job->postId === $duePost->id);
    }
}
