<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Every minute — pick up posts whose scheduled_at has arrived and dispatch publish jobs.
Schedule::command('posts:dispatch-due')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Daily — check platform tokens and notify on expiry.
Schedule::command('platforms:check-tokens')
    ->dailyAt('07:00')
    ->withoutOverlapping();

// Daily — notify owners whose trial expires in 3 or 1 days.
Schedule::command('workspaces:check-trial-expiry')
    ->dailyAt('09:00')
    ->withoutOverlapping();

// 1st of each month — reset AI generation counters for Basic workspaces.
Schedule::command('usage:reset-monthly')
    ->monthlyOn(1, '00:05')
    ->withoutOverlapping();

// Every Monday at 08:00 — send weekly digest emails to all active workspaces.
Schedule::call(function () {
    \App\Models\Brand::whereHas('workspace', fn ($q) => $q->whereIn('subscription_status', ['active', 'trialing']))
        ->whereHas('workspace', fn ($q) => $q->whereIn('plan', ['pro', 'agency']))
        ->each(function (\App\Models\Brand $brand) {
            $owner = $brand->workspace->users()->where('role', 'owner')->first();
            if ($owner) {
                \Illuminate\Support\Facades\Mail::to($owner->email)->queue(
                    new \App\Mail\WeeklyDigestMail($brand)
                );
            }
        });
})->weeklyOn(1, '08:00'); // Monday 8AM
