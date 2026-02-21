<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Exception;

class LogSuccessfulLogin
{
    protected ActivityService $activity;

    /**
     * Create a new listener instance.
     *
     * @param  ActivityService  $activity
     */
    public function __construct(ActivityService $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Handle the login event.
     *
     * @param  Login  $event
     */
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        try {
            // Log the successful login activity
            $this->activity->logLogin($user);

            // Log additional information
            Log::info('User logged in successfully.', [
                'user_id'   => $user->id,
                'email'     => $user->email,
                'timestamp' => now(),
            ]);
        } catch (Exception $e) {
            // Log the exception details
            Log::error('Failed to log successful login.', [
                'error'     => $e->getMessage(),
                'user_id'   => $user->id ?? null,
                'email'     => $user->email ?? null,
                'timestamp' => now(),
            ]);
        }
    }
}
