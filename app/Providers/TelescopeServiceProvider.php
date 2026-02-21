<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            // Local me sab record ho jaye
            return $isLocal
                || $entry->isReportableException()
                || $entry->isFailedRequest()
                || $entry->isFailedJob()
                || $entry->isScheduledTask()
                || $entry->hasMonitoredTag()
                // Production me normal requests & models bhi capture karna
                || in_array($entry->type, ['query', 'model', 'request', 'view', 'job', 'log', 'exception']);
        });

        // Production me manual enable
        if ($this->app->environment('production') && env('TELESCOPE_ENABLED', false)) {
            $this->app->register(\Laravel\Telescope\TelescopeApplicationServiceProvider::class);
        }
    }

    /**
     * Hide sensitive request data
     */
    protected function hideSensitiveRequestDetails(): void
    {
        Telescope::hideRequestParameters(['_token', 'password', 'password_confirmation']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Admin-only gate
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return $user->is_admin ?? in_array($user->email, ['admin@admin.com']);
        });
    }
}
