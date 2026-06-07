<?php

namespace App\Console\Commands;

use App\Jobs\PublishPostJob;
use App\Models\Post;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('posts:dispatch-due')]
#[Description('Dispatch publish jobs for all posts whose scheduled_at has arrived.')]
class DispatchScheduledPosts extends Command
{
    public function handle(): int
    {
        $due = Post::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->limit(500)
            ->get();

        if ($due->isEmpty()) {
            $this->info('No due posts.');

            return self::SUCCESS;
        }

        foreach ($due as $post) {
            PublishPostJob::dispatch($post->id);
            $this->line("Dispatched: {$post->id}");
        }

        $this->info("Dispatched {$due->count()} post(s).");

        return self::SUCCESS;
    }
}
