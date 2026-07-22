<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CreatorStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorDashboardController extends Controller
{
    protected CreatorStatsService $statsService;

    public function __construct(CreatorStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * GET /api/v1/creator/dashboard/stats
     *
     * Returns Global Statistics & Spotlight Performers for the authenticated content creator.
     */
    public function stats(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'content_type' => 'nullable|string|in:all,post,video,reel',
        ]);

        $userId = $request->user()->id;
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $contentType = $request->query('content_type', 'all');

        $globalStats = $this->statsService->getGlobalStats($userId, $dateFrom, $dateTo, $contentType);
        $spotlight = $this->statsService->getSpotlightPerformers($userId, $dateFrom, $dateTo, $contentType);

        return response()->json([
            'success' => true,
            'message' => 'Creator dashboard statistics retrieved successfully.',
            'data' => [
                'global_stats' => $globalStats,
                'spotlight' => $spotlight,
            ],
        ]);
    }

    /**
     * GET /api/v1/creator/dashboard/exclusive-content
     *
     * Returns paginated exclusive content with item-level metrics.
     */
    public function exclusiveContent(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'content_type' => 'nullable|string|in:all,post,video,reel',
            'sort_by' => 'nullable|string|in:views,earnings,purchases',
            'order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $userId = $request->user()->id;
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $contentType = $request->query('content_type', 'all');
        $sortBy = $request->query('sort_by', 'earnings');
        $order = $request->query('order', 'desc');
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $paginatedItems = $this->statsService->getExclusiveContentStats(
            $userId,
            $dateFrom,
            $dateTo,
            $contentType,
            $sortBy,
            $order,
            $perPage,
            $page
        );

        return response()->json([
            'success' => true,
            'message' => 'Exclusive content statistics retrieved successfully.',
            'data' => $paginatedItems,
        ]);
    }
}
