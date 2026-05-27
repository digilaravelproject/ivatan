<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Ecommerce\UserOrder;
use App\Models\Like;
use App\Models\UserPost;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HistoryService
{
    public function getLikes(int $userId, Request $request)
    {
        $perPage = min($request->integer('per_page', 20), 50);

        return Like::select('id', 'user_id', 'likeable_type', 'likeable_id', 'created_at')
            ->where('user_id', $userId)
            ->with(['likeable' => function ($morph) {
                $morph->morphWith([
                    UserPost::class => ['media'],
                ]);
            }])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->cursorPaginate($perPage);
    }

    public function getComments(int $userId, Request $request)
    {
        $perPage = min($request->integer('per_page', 20), 50);

        return Comment::select('id', 'user_id', 'body', 'commentable_type', 'commentable_id', 'parent_id', 'created_at')
            ->where('user_id', $userId)
            ->with(['commentable' => function ($morph) {
                $morph->morphWith([
                    UserPost::class => ['media'],
                ]);
            }])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->cursorPaginate($perPage);
    }

    public function getVideoViews(int $userId, Request $request)
    {
        $perPage = min($request->integer('per_page', 20), 50);

        $allowedTypes = match ($request->filter) {
            'reels'      => ['reel'],
            'long_video' => ['video'],
            default      => ['reel', 'video'],
        };

        return View::select('views.id', 'views.user_id', 'views.viewable_type', 'views.viewable_id', 'views.created_at')
            ->join('user_posts', function ($join) {
                $join->on('views.viewable_id', '=', 'user_posts.id')
                    ->where('views.viewable_type', '=', (new UserPost)->getMorphClass());
            })
            ->where('views.user_id', $userId)
            ->whereIn('user_posts.type', $allowedTypes)
            ->selectRaw('user_posts.type as post_type')
            ->selectRaw('user_posts.caption as post_caption')
            ->with(['viewable' => function ($m) {
                $m->with(['media']);
            }])
            ->orderByDesc('views.created_at')
            ->orderByDesc('views.id')
            ->cursorPaginate($perPage);
    }

    public function getPurchases(int $userId, Request $request)
    {
        $perPage = min($request->integer('per_page', 20), 50);

        return UserOrder::select('id', 'uuid', 'total_amount', 'status', 'created_at')
            ->where('buyer_id', $userId)
            ->whereIn('status', ['paid', 'delivered'])
            ->whereHas('items', fn($q) => $q->where('item_type', 'user_products'))
            ->with(['items' => function ($q) {
                $q->where('item_type', 'user_products')
                    ->with(['item']);
            }])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->cursorPaginate($perPage);
    }

    public function getServices(int $userId, Request $request)
    {
        $perPage = min($request->integer('per_page', 20), 50);

        return UserOrder::select('id', 'uuid', 'total_amount', 'status', 'created_at')
            ->where('buyer_id', $userId)
            ->whereIn('status', ['paid', 'delivered'])
            ->whereHas('items', fn($q) => $q->where('item_type', 'user_services'))
            ->with(['items' => function ($q) {
                $q->where('item_type', 'user_services')
                    ->with(['item']);
            }])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->cursorPaginate($perPage);
    }
}
