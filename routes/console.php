<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\UpdatePostTrendingScore;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::job(new UpdatePostTrendingScore)->everyThirtyMinutes();

// Auto-restore abandoned carts (30+ mins old pending orders)
Schedule::command('orders:restore-abandoned')->everyThirtyMinutes();

// Expire past-due subscriptions daily
Schedule::command('subscriptions:expire')->daily();

// Payment gateway health check (hourly — logs only, no external alerting)
Schedule::command('payments:health-check')->hourly()->withoutOverlapping();

// Cleanup stale online presence (mark offline if last_seen_at > 3 min ago)
Schedule::command('presence:cleanup-stale')->everyMinute()->withoutOverlapping();
