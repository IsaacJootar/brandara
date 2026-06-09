<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('usage:reset-monthly')]
#[Description('Reset monthly AI generation counters for all Basic (starter) workspaces. Runs on the 1st of each month.')]
class ResetMonthlyUsage extends Command
{
    public function handle(): int
    {
        $count = Workspace::where('plan', 'starter')
            ->where('ai_generations_used', '>', 0)
            ->update([
                'ai_generations_used' => 0,
                'usage_reset_date' => now()->startOfMonth()->toDateString(),
            ]);

        $this->info("Reset generation counters for {$count} Basic workspace(s).");

        return Command::SUCCESS;
    }
}
