<?php

namespace App\Services;

use App\Models\UserPost;
use App\Models\ExclusiveContentPurchase;
use App\Models\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CreatorStatsService
{
    /**
     * Get Global Dashboard Statistics for a Content Creator.
     */
    public function getGlobalStats(int $userId, ?string $dateFrom = null, ?string $dateTo = null, string $contentType = 'all'): array
    {
        $postsQuery = UserPost::query()
            ->where('user_id', $userId)
            ->where('is_exclusive', true)
            ->where('status', 'active');

        if ($contentType !== 'all' && in_array($contentType, ['post', 'video', 'reel'])) {
            $postsQuery->where('type', $contentType);
        }

        $exclusivePostIds = (clone $postsQuery)->pluck('id')->toArray();
        $totalExclusiveContent = count($exclusivePostIds);

        if (empty($exclusivePostIds)) {
            return [
                'global_total_views' => 0,
                'global_total_earnings' => 0.00,
                'global_total_purchases' => 0,
                'global_total_exclusive_content' => 0,
            ];
        }

        // Purchases Query
        $purchasesQuery = ExclusiveContentPurchase::query()
            ->whereIn('user_post_id', $exclusivePostIds)
            ->whereIn('status', ['completed', 'success']);

        if ($dateFrom) {
            $purchasesQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $purchasesQuery->whereDate('created_at', '<=', $dateTo);
        }

        $globalEarnings = (float) $purchasesQuery->sum('creator_price');
        $globalPurchases = (int) $purchasesQuery->count();

        // Views Query
        $viewsQuery = View::query()
            ->where('viewable_type', UserPost::class)
            ->whereIn('viewable_id', $exclusivePostIds);

        if ($dateFrom) {
            $viewsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $viewsQuery->whereDate('created_at', '<=', $dateTo);
        }

        $dateFilteredViews = $viewsQuery->count();

        // If no date range filter is provided, fall back to aggregate view_count from user_posts if higher
        if (!$dateFrom && !$dateTo) {
            $aggregatedPostViews = (int) (clone $postsQuery)->sum('view_count');
            $globalViews = max($dateFilteredViews, $aggregatedPostViews);
        } else {
            $globalViews = $dateFilteredViews;
        }

        return [
            'global_total_views' => $globalViews,
            'global_total_earnings' => round($globalEarnings, 2),
            'global_total_purchases' => $globalPurchases,
            'global_total_exclusive_content' => $totalExclusiveContent,
        ];
    }

    /**
     * Get Spotlight Highlights (Most Viewed, Most Purchased, Highest Earning).
     */
    public function getSpotlightPerformers(int $userId, ?string $dateFrom = null, ?string $dateTo = null, string $contentType = 'all'): array
    {
        $postsQuery = UserPost::query()
            ->where('user_id', $userId)
            ->where('is_exclusive', true)
            ->where('status', 'active');

        if ($contentType !== 'all' && in_array($contentType, ['post', 'video', 'reel'])) {
            $postsQuery->where('type', $contentType);
        }

        $posts = $postsQuery->get();

        if ($posts->isEmpty()) {
            return [
                'most_viewed' => null,
                'most_purchased' => null,
                'highest_earning' => null,
            ];
        }

        $postIds = $posts->pluck('id')->toArray();

        // Compute metrics for each post
        $purchasesStats = ExclusiveContentPurchase::query()
            ->select('user_post_id', 
                DB::raw('COUNT(id) as purchase_count'), 
                DB::raw('SUM(creator_price) as total_earnings')
            )
            ->whereIn('user_post_id', $postIds)
            ->whereIn('status', ['completed', 'success'])
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->groupBy('user_post_id')
            ->get()
            ->keyBy('user_post_id');

        $viewsStats = View::query()
            ->select('viewable_id', DB::raw('COUNT(id) as view_count'))
            ->where('viewable_type', UserPost::class)
            ->whereIn('viewable_id', $postIds)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->groupBy('viewable_id')
            ->get()
            ->keyBy('viewable_id');

        $enrichedPosts = $posts->map(function ($post) use ($purchasesStats, $viewsStats, $dateFrom, $dateTo) {
            $pStat = $purchasesStats->get($post->id);
            $vStat = $viewsStats->get($post->id);

            $purchasesCount = $pStat ? (int) $pStat->purchase_count : 0;
            $earnings = $pStat ? (float) $pStat->total_earnings : 0.0;
            
            if ($dateFrom || $dateTo) {
                $viewsCount = $vStat ? (int) $vStat->view_count : 0;
            } else {
                $viewsCount = max($vStat ? (int) $vStat->view_count : 0, (int) $post->view_count);
            }

            return [
                'id' => $post->id,
                'uuid' => $post->uuid,
                'type' => $post->type,
                'caption' => $post->caption,
                'price' => (float) $post->price,
                'created_at' => $post->created_at?->toDateTimeString(),
                'views_count' => $viewsCount,
                'purchases_count' => $purchasesCount,
                'total_earnings' => round($earnings, 2),
                'thumbnail_url' => $post->images->first()['thumb_url'] ?? null,
            ];
        });

        $mostViewed = $enrichedPosts->sortByDesc('views_count')->first();
        $mostPurchased = $enrichedPosts->sortByDesc('purchases_count')->first();
        $highestEarning = $enrichedPosts->sortByDesc('total_earnings')->first();

        return [
            'most_viewed' => ($mostViewed && $mostViewed['views_count'] > 0) ? $mostViewed : null,
            'most_purchased' => ($mostPurchased && $mostPurchased['purchases_count'] > 0) ? $mostPurchased : null,
            'highest_earning' => ($highestEarning && $highestEarning['total_earnings'] > 0) ? $highestEarning : null,
        ];
    }

    /**
     * Get Paginated Exclusive Content List with Per-Item Detailed Statistics.
     */
    public function getExclusiveContentStats(
        int $userId,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        string $contentType = 'all',
        string $sortBy = 'earnings',
        string $order = 'desc',
        int $perPage = 15,
        int $page = 1
    ): LengthAwarePaginator {
        $postsQuery = UserPost::query()
            ->where('user_id', $userId)
            ->where('is_exclusive', true)
            ->where('status', 'active');

        if ($contentType !== 'all' && in_array($contentType, ['post', 'video', 'reel'])) {
            $postsQuery->where('type', $contentType);
        }

        $allPosts = $postsQuery->get();
        $postIds = $allPosts->pluck('id')->toArray();

        if (empty($postIds)) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, $perPage, $page, ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        // Purchases data
        $purchasesQuery = ExclusiveContentPurchase::query()
            ->select(
                'user_post_id',
                DB::raw('COUNT(id) as total_purchases'),
                DB::raw('SUM(creator_price) as total_earnings'),
                DB::raw('COUNT(DISTINCT buyer_id) as total_purchase_users')
            )
            ->whereIn('user_post_id', $postIds)
            ->whereIn('status', ['completed', 'success']);

        if ($dateFrom) {
            $purchasesQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $purchasesQuery->whereDate('created_at', '<=', $dateTo);
        }

        $purchasesData = $purchasesQuery->groupBy('user_post_id')->get()->keyBy('user_post_id');

        // All Buyers per post map
        $postBuyers = ExclusiveContentPurchase::query()
            ->select('user_post_id', 'buyer_id')
            ->whereIn('user_post_id', $postIds)
            ->whereIn('status', ['completed', 'success'])
            ->get()
            ->groupBy('user_post_id');

        // Views data
        $viewsQuery = View::query()
            ->select('viewable_id', 'user_id')
            ->where('viewable_type', UserPost::class)
            ->whereIn('viewable_id', $postIds);

        if ($dateFrom) {
            $viewsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $viewsQuery->whereDate('created_at', '<=', $dateTo);
        }

        $viewsGrouped = $viewsQuery->get()->groupBy('viewable_id');

        $enriched = $allPosts->map(function ($post) use ($purchasesData, $postBuyers, $viewsGrouped, $dateFrom, $dateTo) {
            $pData = $purchasesData->get($post->id);
            $pBuyers = $postBuyers->get($post->id)?->pluck('buyer_id')->unique()->toArray() ?? [];
            $postViewsCollection = $viewsGrouped->get($post->id) ?? collect([]);

            $totalPurchases = $pData ? (int) $pData->total_purchases : 0;
            $totalEarnings = $pData ? (float) $pData->total_earnings : 0.0;
            $totalPurchaseUsers = $pData ? (int) $pData->total_purchase_users : 0;

            if ($dateFrom || $dateTo) {
                $totalViews = $postViewsCollection->count();
            } else {
                $totalViews = max($postViewsCollection->count(), (int) $post->view_count);
            }

            // Purchased User Views: views count specifically from buyers
            $purchasedUserViews = 0;
            if (!empty($pBuyers)) {
                $purchasedUserViews = $postViewsCollection->filter(function ($view) use ($pBuyers) {
                    return $view->user_id && in_array($view->user_id, $pBuyers);
                })->count();
            }

            return [
                'id' => $post->id,
                'uuid' => $post->uuid,
                'type' => $post->type,
                'caption' => $post->caption,
                'price' => (float) $post->price,
                'created_at' => $post->created_at?->toDateTimeString(),
                'total_views' => $totalViews,
                'total_earnings' => round($totalEarnings, 2),
                'total_purchase_count' => $totalPurchases,
                'total_purchase_users' => $totalPurchaseUsers,
                'purchased_user_views' => $purchasedUserViews,
                'thumbnail_url' => $post->images->first()['thumb_url'] ?? null,
            ];
        });

        // Sorting logic
        $descending = strtolower($order) === 'desc';
        switch ($sortBy) {
            case 'views':
                $sorted = $enriched->sortBy('total_views', SORT_REGULAR, $descending);
                break;
            case 'purchases':
                $sorted = $enriched->sortBy('total_purchase_count', SORT_REGULAR, $descending);
                break;
            case 'earnings':
            default:
                $sorted = $enriched->sortBy('total_earnings', SORT_REGULAR, $descending);
                break;
        }

        // Pagination
        $total = $sorted->count();
        $offset = ($page - 1) * $perPage;
        $itemsForPage = $sorted->slice($offset, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
