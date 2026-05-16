<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\UpdatePostTrendingScore;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::job(new UpdatePostTrendingScore)->everyThirtyMinutes();

Schedule::command('queue:work --queue=default,database --stop-when-empty --tries=3 --timeout=120')
    ->everyMinute()
    ->withoutOverlapping();

// Auto-restore abandoned carts (30+ mins old pending orders)
Schedule::command('orders:restore-abandoned')->everyThirtyMinutes();
