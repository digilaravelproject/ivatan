<?php

namespace App\Observers;

use App\Models\UserPost;
use App\Services\ViewTrackingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserPostObserver
{
    protected $viewTrackingService;

    public function __construct(ViewTrackingService $viewTrackingService)
    {
        $this->viewTrackingService = $viewTrackingService;
    }

    /**
     * Handle the UserPost "created" event.
     */
    public function created(UserPost $userPost): void
    {
        //
    }

    /**
     * Handle the UserPost "updating" event.
     */
    public function updating(UserPost $userPost): void
    {
        // If price has changed, we must revert the status to pending verification
        if ($userPost->isDirty('price') || $userPost->isDirty('is_exclusive')) {
            if ($userPost->is_exclusive && (float) $userPost->price > 0) {
                $userPost->exclusive_status = 'pending';
            } else {
                $userPost->exclusive_status = null;
                $userPost->override_platform_fee = null;
                $userPost->override_platform_fee_type = null;
            }
        }
    }

    /**
     * Handle the UserPost "updated" event.
     */
    public function updated(UserPost $userPost): void
    {
        // Check if exclusive_status changed from pending to approved
        if ($userPost->isDirty('exclusive_status') && $userPost->exclusive_status === 'approved') {
            Log::info("Exclusive content approved for post ID: {$userPost->id}");
            // Trigger notifications to user, etc.
        }
    }

    /**
     * Handle the UserPost "deleted" event.
     */
    public function deleted(UserPost $userPost): void
    {
        //
    }

    /**
     * Handle the UserPost "restored" event.
     */
    public function restored(UserPost $userPost): void
    {
        //
    }

    /**
     * Handle the UserPost "force deleted" event.
     */
    public function forceDeleted(UserPost $userPost): void
    {
        //
    }

    public function retrieved(UserPost $userPost): void
    {
        // Only track if it's a GET request and the user is not an admin
        // Guests (unauthenticated users) are tracked by IP in the service
        $user = Auth::user();
        if (request()->isMethod('get') && (!$user || !$user->is_admin)) {
            $this->viewTrackingService->track($userPost, request());
        }
    }
}
