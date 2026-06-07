<?php

namespace App\Services\Platforms\Publishers;

use App\Models\PlatformConnection;
use App\Models\Post;

interface PlatformPublisher
{
    /**
     * Publish a post to the given platform connection.
     * Implementations MUST never throw on expected platform errors —
     * wrap them as PublishResult::fail() so the job can classify and retry.
     */
    public function publish(Post $post, PlatformConnection $connection, string $body): PublishResult;
}
