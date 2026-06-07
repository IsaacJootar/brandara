<?php

namespace App\Services\Platforms\Publishers;

use App\Models\PlatformConnection;
use App\Models\Post;
use Illuminate\Support\Str;

/**
 * Development-mode publisher.
 *
 * Pretends to publish a post and returns a fake live URL.
 * Used until real platform OAuth dev apps are wired up.
 */
class FakePublisher implements PlatformPublisher
{
    public function publish(Post $post, PlatformConnection $connection, string $body): PublishResult
    {
        $fakeId = Str::lower(Str::random(10));

        return PublishResult::ok(
            "https://{$connection->platform}.example/post/{$fakeId}"
        );
    }
}
