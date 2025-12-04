<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Telescope\AuthorizesRequests;

class FollowController extends Controller
{
    use AuthorizesRequests;
    /**
     * Follow a user.
     *
     * @param Request $request
     * @param int|string $userId
     * @return JsonResponse
     */
    public function follow(Request $request, $userId): JsonResponse
    {
        // 1. Start Transaction for Data Integrity
        DB::beginTransaction();

        try {
            $authUser = Auth::user();

            if (!$authUser) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            // Self follow check
            if ((int)$authUser->id === (int)$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot follow yourself.',
                    'code' => 'SELF_ACTION_FORBIDDEN'
                ], 422);
            }

            $userToFollow = User::lockForUpdate()->find($userId); // lockForUpdate prevents race conditions

            if (!$userToFollow) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            // Already following check
            if ($authUser->isFollowing($userToFollow)) {
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Already following.',
                    'code' => 'ALREADY_FOLLOWING'
                ], 200);
            }

            // 2. Main Logic: Attach & Increment
            $authUser->following()->attach($userId);

            // Manual Increment (Fast & Reliable)
            $authUser->increment('following_count');
            $userToFollow->increment('followers_count');

            // 3. Commit Transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Followed successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Kuch bhi galat hua to database purani state me aa jayega
            Log::error("Follow Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
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
        DB::beginTransaction();

        try {
            $authUser = Auth::user();

            if (!$authUser) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            if ((int)$authUser->id === (int)$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot unfollow yourself.',
                    'code' => 'SELF_ACTION_FORBIDDEN'
                ], 422);
            }

            $userToUnfollow = User::lockForUpdate()->find($userId);

            if (!$userToUnfollow) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            // Check relation
            if (!$authUser->isFollowing($userToUnfollow)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'You are not following this user.',
                    'code' => 'NOT_FOLLOWING'
                ], 400);
            }

            // 2. Main Logic: Detach & Decrement
            $authUser->following()->detach($userId);

            // Manual Decrement (Ensure count doesn't go below 0)
            if ($authUser->following_count > 0) {
                $authUser->decrement('following_count');
            }
            if ($userToUnfollow->followers_count > 0) {
                $userToUnfollow->decrement('followers_count');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unfollowed successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Unfollow Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get list of followers.
     *
     * @param int|string $userId
     * @return JsonResponse
     */
    public function getFollowers($userId): JsonResponse
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            // Using simplePaginate is better for performance than get() for lists
            $followers = $user->followers()
                ->select(['users.id', 'users.name', 'users.uuid', 'users.username', 'users.profile_photo_path'])
                ->simplePaginate(50);

            return response()->json([
                'success' => true,
                'data' => $followers
            ], 200);
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
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }

            $following = $user->following()
                ->select(['users.id', 'users.name', 'users.uuid', 'users.username', 'users.profile_photo_path'])
                ->simplePaginate(50);

            return response()->json([
                'success' => true,
                'data' => $following
            ], 200);
        } catch (\Exception $e) {
            Log::error("Get Following Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }
}
