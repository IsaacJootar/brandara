<?php

namespace App\Services;

use App\Models\PlatformConnection;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\ApprovalNeededNotification;
use App\Notifications\PostFailedNotification;
use App\Notifications\TokenExpiredNotification;
use App\Notifications\TrialExpiringNotification;

class NotificationService
{
    /**
     * Notify all workspace users that a post failed to publish.
     * Called from PublishPostJob after exhausting retries.
     */
    public function postFailed(Post $post): void
    {
        $users = $post->brand->workspace->users;

        foreach ($users as $user) {
            $user->notify(new PostFailedNotification($post));
        }
    }

    /**
     * Notify workspace owner that their trial is about to expire.
     * Called by the CheckTrialExpiry scheduled command.
     */
    public function trialExpiring(Workspace $workspace, int $daysLeft): void
    {
        $owner = $workspace->users()->where('role', 'owner')->first();

        if ($owner) {
            $owner->notify(new TrialExpiringNotification($workspace, $daysLeft));
        }
    }

    /**
     * Notify workspace users that a platform token has expired.
     * Called by CheckPlatformTokens scheduled command.
     */
    public function tokenExpired(PlatformConnection $connection): void
    {
        $users = $connection->brand->workspace->users;

        foreach ($users as $user) {
            $user->notify(new TokenExpiredNotification($connection));
        }
    }

    /**
     * Notify a specific user that a post needs their approval.
     */
    public function approvalNeeded(Post $post, User $submittedBy, User $approver): void
    {
        $approver->notify(new ApprovalNeededNotification($post, $submittedBy));
    }
}
