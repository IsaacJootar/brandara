<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use App\Services\NotificationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('workspaces:check-trial-expiry')]
#[Description('Notify workspace owners whose trial expires in 3 days or 1 day.')]
class CheckTrialExpiry extends Command
{
    public function handle(NotificationService $notifications): int
    {
        $workspaces = Workspace::where('subscription_status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->get()
            ->filter(fn ($w) => in_array($w->trialDaysLeft(), [3, 1]));

        foreach ($workspaces as $workspace) {
            $notifications->trialExpiring($workspace, $workspace->trialDaysLeft());
            $this->line("Notified: {$workspace->name} ({$workspace->trialDaysLeft()} days left)");
        }

        $this->info("Checked {$workspaces->count()} expiring trial(s).");

        return self::SUCCESS;
    }
}
