<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserFollowResource;
use App\Services\FollowService; // Service import

class FollowController extends Controller
{
    protected $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    /**
     * Follow a user.
     *
     * @param Request $request
     * @param int|string $userId
     * @return JsonResponse
     */
    public function follow(Request $request, $userId): JsonResponse
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $result = $this->followService->followUser($authUser, $userId);

        if ($result['success']) {
            // Handle success (including 'Already Following')
            $statusCode = ($result['code'] ?? null) === 'ALREADY_FOLLOWING' ? 200 : 200;
            return response()->json($result, $statusCode);
        }

        // Handle errors based on code
        $statusCode = match ($result['code'] ?? 'SERVER_ERROR') {
            'NOT_FOUND' => 404,
            'SELF_ACTION_FORBIDDEN' => 422,
            default => 500,
        };

        return response()->json($result, $statusCode);
    }

    /**
     * Unfollow a user.
     *
     * @param Request $request
     * @param int|string $userId
     * @return JsonResponse
     */
    public function unfollow(Request $request, $userId): JsonResponse
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $result = $this->followService->unfollowUser($authUser, $userId);

        if ($result['success']) {
            return response()->json($result, 200);
        }

        // Handle errors based on code
        $statusCode = match ($result['code'] ?? 'SERVER_ERROR') {
            'NOT_FOUND' => 404,
            'SELF_ACTION_FORBIDDEN' => 422,
            'NOT_FOLLOWING' => 400,
            default => 500,
        };

        return response()->json($result, $statusCode);
    }

    // --------------------------------------------------------------------------
    // List Methods (Optimized with DB::raw for follow status check)
    // --------------------------------------------------------------------------

    /**
     * Get list of followers.
     *
     * @param int|string $userId
     * @return JsonResponse
     */
    public function getFollowers($userId): JsonResponse
    {
        try {
            // Only fetch ID to find user
            $user = User::select(['id'])->find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            $authUserId = Auth::id();

            // Select minimal columns required for the resource
            $selectColumns = ['users.id', 'users.name', 'users.username', 'users.profile_photo_path', 'users.is_verified'];

            $followersQuery = $user->followers()->select($selectColumns);

            // Efficiently check if the authenticated user follows the follower
            if ($authUserId) {
                $followersQuery->leftJoin('followers as auth_follows', function ($join) use ($authUserId) {
                    $join->on('users.id', '=', 'auth_follows.following_id')
                        ->where('auth_follows.follower_id', '=', $authUserId);
                })
                    ->addSelect(DB::raw('auth_follows.follower_id IS NOT NULL as is_followed_by_auth_user'));
            } else {
                $followersQuery->addSelect(DB::raw('FALSE as is_followed_by_auth_user'));
            }

            $followers = $followersQuery->simplePaginate(50);

            // Use the lean UserFollowResource
            return UserFollowResource::collection($followers)->response();
        } catch (\Exception $e) {
            Log::error("Get Followers Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

    /**
     * Get list of following.
     *
     * @param int|string $userId
     * @return JsonResponse
     */
    public function getFollowing($userId): JsonResponse
    {
        try {
            // Only fetch ID to find user
            $user = User::select(['id'])->find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            $authUserId = Auth::id();

            // Select minimal columns required for the resource
            $selectColumns = ['users.id', 'users.name', 'users.username', 'users.profile_photo_path', 'users.is_verified'];

            $followingQuery = $user->following()->select($selectColumns);

            // Efficiently check if the authenticated user follows the following user
            if ($authUserId) {
                $followingQuery->leftJoin('followers as auth_follows', function ($join) use ($authUserId) {
                    $join->on('users.id', '=', 'auth_follows.following_id')
                        ->where('auth_follows.follower_id', '=', $authUserId);
                })
                    ->addSelect(DB::raw('auth_follows.follower_id IS NOT NULL as is_followed_by_auth_user'));
            } else {
                $followingQuery->addSelect(DB::raw('FALSE as is_followed_by_auth_user'));
            }

            $following = $followingQuery->simplePaginate(50);

            // Use the lean UserFollowResource
            return UserFollowResource::collection($following)->response();
        } catch (\Exception $e) {
            Log::error("Get Following Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }
}
