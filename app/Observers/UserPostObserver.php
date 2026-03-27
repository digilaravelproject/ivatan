<?php

namespace App\Observers;

use App\Models\UserPost;
use App\Services\ViewTrackingService;
use Illuminate\Support\Facades\Auth;

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
     * Handle the UserPost "updated" event.
     */
    public function updated(UserPost $userPost): void
    {
        //
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
