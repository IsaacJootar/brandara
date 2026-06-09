<?php

namespace App\Services\Engagement;

use App\Models\EngagementAction;

/**
 * Fake engagement publisher — simulates auto-like and auto-comment
 * without real API calls. Mirrors FakePublisher pattern used for posts.
 *
 * Activated when config('services.engagement.live') is false (default).
 * When real OAuth apps are approved, swap this for LiveEngagementPublisher.
 */
class FakeEngagementPublisher
{
    public function like(EngagementAction $action): bool
    {
        // Simulate success — log for visibility during development
        \Log::info('[FakeEngagementPublisher] Would like post', [
            'platform' => $action->platform,
            'post_id' => $action->target_post_id,
            'account' => $action->target_account,
            'brand_id' => $action->brand_id,
        ]);

        return true;
    }

    public function comment(EngagementAction $action): bool
    {
        \Log::info('[FakeEngagementPublisher] Would post comment', [
            'platform' => $action->platform,
            'post_id' => $action->target_post_id,
            'comment' => $action->comment_body,
            'brand_id' => $action->brand_id,
        ]);

        return true;
    }
}
