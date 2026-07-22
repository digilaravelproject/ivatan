<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CreatorStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatorDashboardWebController extends Controller
{
    protected CreatorStatsService $statsService;

    public function __construct(CreatorStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index(Request $request): View
    {
        $userId = auth()->id() ?? 1; // Default fallback to user 1 for direct browser preview if unauthenticated
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $contentType = $request->query('content_type', 'all');
        $sortBy = $request->query('sort_by', 'earnings');
        $order = $request->query('order', 'desc');
        $page = (int) $request->query('page', 1);

        $globalStats = $this->statsService->getGlobalStats($userId, $dateFrom, $dateTo, $contentType);
        $spotlight = $this->statsService->getSpotlightPerformers($userId, $dateFrom, $dateTo, $contentType);
        $exclusiveContent = $this->statsService->getExclusiveContentStats(
            $userId,
            $dateFrom,
            $dateTo,
            $contentType,
            $sortBy,
            $order,
            15,
            $page
        );

        return view('web.creator_dashboard', compact(
            'globalStats',
            'spotlight',
            'exclusiveContent',
            'dateFrom',
            'dateTo',
            'contentType',
            'sortBy',
            'order'
        ));
    }
}
