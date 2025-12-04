<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class CacheClearController extends Controller
{
    public function clearAllCache()
    {
        // AM/PM format time
        $cleared_at = Carbon::now()
            ->setTimezone('Asia/Kolkata')
            ->format('Y-m-d h:i:s A');

        // Run cache clear commands
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('config:cache');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');

        return response()->json([
            'status' => 'success',
            'message' => 'Cache cleared successfully!',
            'cleared_at' => $cleared_at,
            'cleared_items' => [
                'application_cache' => true,
                'config_cache'      => true,
                'route_cache'       => true,
                'view_cache'        => true,
                'optimize_cache'    => true
            ]
        ]);
    }
}
