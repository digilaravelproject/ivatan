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
