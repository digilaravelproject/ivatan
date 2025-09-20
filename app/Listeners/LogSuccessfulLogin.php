<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;
use Exception;

class LogSuccessfulLogin
{
    protected $activity;

    /**
     * Create a new listener instance.
     *
     * @param  \App\Services\ActivityService  $activity
     * @return void
     */
    public function __construct(ActivityService $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        try {
            // Log the successful login activity
            $this->activity->logLogin($event->user);

            // Log additional information for debugging or monitoring purposes
            Log::info('User logged in successfully.', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'timestamp' => now(),
            ]);
        } catch (Exception $e) {
            // Handle exceptions and log the error
            Log::error('Failed to log successful login', [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id ?? null,
                'email' => $event->user->email ?? null,
                'timestamp' => now(),
            ]);
        }
    }
}
