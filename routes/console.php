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
