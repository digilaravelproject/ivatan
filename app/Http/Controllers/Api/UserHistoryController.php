<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\History\CommentHistoryResource;
use App\Http\Resources\History\LikeHistoryResource;
use App\Http\Resources\History\PurchaseHistoryResource;
use App\Http\Resources\History\ServiceHistoryResource;
use App\Http\Resources\History\VideoViewHistoryResource;
use App\Services\HistoryService;
use Illuminate\Http\Request;

class UserHistoryController extends Controller
{
    public function __construct(
        protected HistoryService $historyService
    ) {}

    public function likes(Request $request)
    {
        try {
            $likes = $this->historyService->getLikes($request->user()->id, $request);
            return response()->json([
                'success' => true,
                'data' => LikeHistoryResource::collection($likes),
                'meta' => [
                    'next_cursor' => $likes->nextCursor()?->encode(),
                    'per_page' => $likes->perPage(),
                    'has_more' => $likes->hasMorePages(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to fetch like history.', 500);
        }
    }

    public function comments(Request $request)
    {
        try {
            $comments = $this->historyService->getComments($request->user()->id, $request);
            return response()->json([
                'success' => true,
                'data' => CommentHistoryResource::collection($comments),
                'meta' => [
                    'next_cursor' => $comments->nextCursor()?->encode(),
                    'per_page' => $comments->perPage(),
                    'has_more' => $comments->hasMorePages(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to fetch comment history.', 500);
        }
    }

    public function videoViews(Request $request)
    {
        try {
            $filter = $request->filter;
            if ($filter && !in_array($filter, ['reels', 'long_video', 'both'])) {
                return $this->errorResponse('Invalid filter. Use: reels, long_video, or both.', 422);
            }

            $views = $this->historyService->getVideoViews($request->user()->id, $request);
            return response()->json([
                'success' => true,
                'data' => VideoViewHistoryResource::collection($views),
                'meta' => [
                    'next_cursor' => $views->nextCursor()?->encode(),
                    'per_page' => $views->perPage(),
                    'has_more' => $views->hasMorePages(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to fetch video view history.', 500);
        }
    }

    public function purchases(Request $request)
    {
        try {
            $orders = $this->historyService->getPurchases($request->user()->id, $request);
            return response()->json([
                'success' => true,
                'data' => PurchaseHistoryResource::collection($orders),
                'meta' => [
                    'next_cursor' => $orders->nextCursor()?->encode(),
                    'per_page' => $orders->perPage(),
                    'has_more' => $orders->hasMorePages(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to fetch purchase history.', 500);
        }
    }

    public function services(Request $request)
    {
        try {
            $orders = $this->historyService->getServices($request->user()->id, $request);
            return response()->json([
                'success' => true,
                'data' => ServiceHistoryResource::collection($orders),
                'meta' => [
                    'next_cursor' => $orders->nextCursor()?->encode(),
                    'per_page' => $orders->perPage(),
                    'has_more' => $orders->hasMorePages(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to fetch service history.', 500);
        }
    }
}
