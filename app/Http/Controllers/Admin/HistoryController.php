<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HistoryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected HistoryService $historyService
    ) {}

    public function likes(Request $request, User $user)
    {
        $this->authorize('viewHistory', [$user->id]);
        $likes = $this->historyService->getLikes($user->id, $request);
        return view('admin.history.likes', compact('likes', 'user'));
    }

    public function comments(Request $request, User $user)
    {
        $this->authorize('viewHistory', [$user->id]);
        $comments = $this->historyService->getComments($user->id, $request);
        return view('admin.history.comments', compact('comments', 'user'));
    }

    public function videoViews(Request $request, User $user)
    {
        $this->authorize('viewHistory', [$user->id]);
        $views = $this->historyService->getVideoViews($user->id, $request);
        return view('admin.history.video-views', compact('views', 'user'));
    }

    public function purchases(Request $request, User $user)
    {
        $this->authorize('viewHistory', [$user->id]);
        $orders = $this->historyService->getPurchases($user->id, $request);
        return view('admin.history.purchases', compact('orders', 'user'));
    }

    public function services(Request $request, User $user)
    {
        $this->authorize('viewHistory', [$user->id]);
        $orders = $this->historyService->getServices($user->id, $request);
        return view('admin.history.services', compact('orders', 'user'));
    }
}
