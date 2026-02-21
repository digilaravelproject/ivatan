<?php

namespace App\Observers;

use App\Models\UserPost;
use App\Services\ViewTrackingService;

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
        // Check if it's a valid request and if it's not from an admin
        if (request()->isMethod('get') && !auth()->user()->is_admin) {
            $this->viewTrackingService->track($userPost, request());
        }
    }
}
