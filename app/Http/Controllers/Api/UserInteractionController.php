<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Services\BlockService;
use App\Services\BookmarkService;
use App\Services\PostPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserInteractionController extends Controller
{
    protected BookmarkService $bookmarkService;
    protected BlockService $blockService;
    protected PostPreferenceService $preferenceService;

    public function __construct(
        BookmarkService $bookmarkService,
        BlockService $blockService,
        PostPreferenceService $preferenceService
    ) {
        $this->bookmarkService = $bookmarkService;
        $this->blockService = $blockService;
        $this->preferenceService = $preferenceService;
    }

    /**
     * Get the authenticated user.
     */
    private function getAuthUser()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user;
    }

    // =========================================================================
    // BOOKMARK APIs
    // =========================================================================

    /**
     * POST /api/v1/posts/{id}/bookmark
     * Toggle bookmark on/off for a post.
     */
    public function toggleBookmark(int $id): JsonResponse
    {
        try {
            $user = $this->getAuthUser();
            $result = $this->bookmarkService->toggle($user, $id);

            $statusCode = $result['success'] ? 200 : 404;
            return response()->json($result, $statusCode);
        } catch (\Exception $e) {
            Log::error("Toggle Bookmark Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while toggling bookmark.',
            ], 500);
        }
    }

    /**
     * GET /api/v1/user/bookmarks
     * Get user's bookmark collection (paginated).
     * Query params: ?type=post|video|reel|carousel&per_page=15
     */
    public function getBookmarks(Request $request): JsonResponse
    {
        try {
            $user = $this->getAuthUser();

            $type = $request->input('type');
            $perPage = min((int) $request->input('per_page', 15), 50); // Max 50 per page

            $bookmarks = $this->bookmarkService->getCollection($user, $type, $perPage);

            // Transform bookmarks into post resources with bookmark metadata
            $data = $bookmarks->through(function ($bookmark) {
                return [
                    'bookmark_id' => $bookmark->id,
                    'bookmarked_at' => $bookmark->created_at->toIso8601String(),
                    'bookmarked_human' => $bookmark->created_at->diffForHumans(),
                    'post' => new PostResource($bookmark->post),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                    'has_more' => $data->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Get Bookmarks Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching bookmarks.',
            ], 500);
        }
    }

    // =========================================================================
    // BLOCK APIs
    // =========================================================================

    /**
     * POST /api/v1/users/{id}/block
     * Toggle block/unblock on a user.
     */
    public function toggleBlock(int $id): JsonResponse
    {
        try {
            $user = $this->getAuthUser();
            $result = $this->blockService->toggleBlock($user, $id);

            $statusCode = match ($result['code'] ?? 'SERVER_ERROR') {
                'BLOCKED', 'UNBLOCKED' => 200,
                'NOT_FOUND' => 404,
                'SELF_ACTION_FORBIDDEN' => 422,
                default => 500,
            };

            return response()->json($result, $statusCode);
        } catch (\Exception $e) {
            Log::error("Toggle Block Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing block action.',
            ], 500);
        }
    }

    /**
     * GET /api/v1/user/blocked-users
     * List all users blocked by the authenticated user.
     */
    public function getBlockedUsers(Request $request): JsonResponse
    {
        try {
            $user = $this->getAuthUser();
            $perPage = min((int) $request->input('per_page', 20), 50);

            $blockedUsers = $this->blockService->getBlockedUsers($user, $perPage);

            $data = $blockedUsers->through(function ($block) {
                $blocked = $block->blockedUser;
                return [
                    'id' => $blocked->id,
                    'name' => $blocked->name,
                    'username' => $blocked->username,
                    'avatar' => $blocked->profile_photo_url,
                    'is_verified' => $blocked->is_verified ?? false,
                    'blocked_at' => $block->created_at->toIso8601String(),
                    'blocked_human' => $block->created_at->diffForHumans(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                    'has_more' => $data->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Get Blocked Users Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching blocked users.',
            ], 500);
        }
    }

    // =========================================================================
    // INTERESTED / NOT INTERESTED APIs
    // =========================================================================

    /**
     * POST /api/v1/posts/{id}/interested
     * Mark a post as "interested" to see more similar content.
     */
    public function markInterested(int $id): JsonResponse
    {
        try {
            $user = $this->getAuthUser();
            $result = $this->preferenceService->markInterested($user, $id);

            $statusCode = $result['success'] ? 200 : ($result['message'] === 'Post not found.' ? 404 : 422);
            return response()->json($result, $statusCode);
        } catch (\Exception $e) {
            Log::error("Mark Interested Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
            ], 500);
        }
    }

    /**
     * POST /api/v1/posts/{id}/not-interested
     * Mark a post as "not interested" to see less similar content.
     */
    public function markNotInterested(int $id): JsonResponse
    {
        try {
            $user = $this->getAuthUser();
            $result = $this->preferenceService->markNotInterested($user, $id);

            $statusCode = $result['success'] ? 200 : ($result['message'] === 'Post not found.' ? 404 : 422);
            return response()->json($result, $statusCode);
        } catch (\Exception $e) {
            Log::error("Mark Not Interested Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
            ], 500);
        }
    }

    /**
     * DELETE /api/v1/posts/{id}/preference
     * Remove any preference (interested/not_interested) from a post.
     */
    public function removePreference(int $id): JsonResponse
    {
        try {
            $user = $this->getAuthUser();
            $result = $this->preferenceService->removePreference($user, $id);

            return response()->json($result, $result['success'] ? 200 : 500);
        } catch (\Exception $e) {
            Log::error("Remove Preference Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
            ], 500);
        }
    }
}
