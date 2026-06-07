<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\Platforms\Publishers\PublisherFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class PublishPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Layer 1: silent retry schedule (seconds). */
    public array $backoff = [120, 300, 900];

    /** Total attempts including the first try. */
    public int $tries = 4;

    public function __construct(public string $postId) {}

    public function handle(PublisherFactory $factory): void
    {
        /** @var Post|null $post */
        $post = Post::find($this->postId);

        if (! $post || ! in_array($post->status, ['scheduled', 'failed'])) {
            return; // nothing to do — cancelled, deleted, or already published
        }

        $platformContents = $post->platform_contents ?? [];
        $connections = $post->brand->platformConnections()
            ->where('status', 'connected')
            ->get()
            ->keyBy('platform');

        if ($connections->isEmpty()) {
            $this->markFailed($post, 'token_expired', 'No connected platforms. Reconnect in Connections.');

            return;
        }

        $liveUrls = $post->live_post_urls ?? [];
        $errors = [];

        foreach ($platformContents as $platform => $payload) {
            // Already published to this platform in a prior attempt — skip.
            if (isset($liveUrls[$platform])) {
                continue;
            }

            $connection = $connections->get($platform);
            if (! $connection) {
                $errors[$platform] = ['token_expired', "{$platform} is not connected."];

                continue;
            }

            $body = $payload['body'] ?? $post->raw_input ?? '';

            $result = $factory->for($platform)->publish($post, $connection, $body);

            if ($result->success) {
                $liveUrls[$platform] = $result->liveUrl;
                $connection->update(['last_posted_at' => now()]);
            } else {
                $errors[$platform] = [$result->errorCode ?? 'unknown', $result->errorMessage ?? 'Unknown error.'];
            }
        }

        if (empty($errors)) {
            $post->update([
                'status' => 'published',
                'published_at' => now(),
                'live_post_urls' => $liveUrls,
                'failure_reason' => null,
            ]);

            return;
        }

        // Partial success — preserve any successful URLs.
        $post->live_post_urls = $liveUrls;

        // Layer 2 — classify the first error to decide retry behaviour.
        [$code, $message] = collect($errors)->first();
        $reasonSummary = collect($errors)
            ->map(fn ($e, $platform) => ucfirst($platform).': '.$e[1])
            ->implode(' · ');

        if (in_array($code, ['token_expired', 'media_rejected'])) {
            // Don't retry — needs user action.
            $this->markFailed($post, $code, $reasonSummary);

            return;
        }

        // Retryable. If we're out of attempts, escalate to Layer 3 (user-visible failed).
        if ($this->attempts() >= $this->tries) {
            $this->markFailed($post, $code, $reasonSummary);

            return;
        }

        $post->increment('retry_count');
        $delay = $code === 'rate_limited' ? 1800 : ($this->backoff[$this->attempts() - 1] ?? 900);
        $this->release($delay);
    }

    public function failed(?Throwable $e): void
    {
        $post = Post::find($this->postId);
        if (! $post) {
            return;
        }

        $this->markFailed($post, 'unknown', $e?->getMessage() ?? 'Unexpected failure while publishing.');
    }

    private function markFailed(Post $post, string $code, string $message): void
    {
        $post->update([
            'status' => 'failed',
            'failure_reason' => $message,
        ]);

        // Notification dispatch (Layer 3) is handled in a follow-up module.
    }
}
