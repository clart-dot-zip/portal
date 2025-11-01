<?php

use Illuminate\Support\Facades\Schedule;

// Schedule Authentik sync to run every hour
Schedule::command('authentik:sync --all')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/authentik-sync.log'));

Schedule::command('pim:enforce')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/pim-enforce.log'));
