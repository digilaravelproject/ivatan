<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request, $userId)
    {
        $authUser = auth()->user();

        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($authUser->id == $userId) {
            return response()->json(['error' => 'Invalid follow action.'], 400);
        }

        // Use the `isFollowing` helper method in User model
        if ($authUser->isFollowing($user)) {
            return response()->json(['message' => 'Already following.'], 200);
        }

        $authUser->following()->attach($userId);  // Using the Eloquent relationship

        return response()->json(['message' => 'Followed successfully.']);
    }

    public function follow_OLD(Request $request, $userId)
    {
        $authUser = auth()->user();

        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($authUser->id == $userId) {
            return response()->json(['error' => 'Invalid follow action.'], 400);
        }

        $exists = Follower::where('follower_id', $authUser->id)
            ->where('following_id', $userId)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Already following.'], 200);
        }

        Follower::create([
            'follower_id' => $authUser->id,
            'following_id' => $userId,
        ]);

        return response()->json(['message' => 'Followed successfully.']);
    }
    public function unfollow(Request $request, $userId)
    {
        $authUser = auth()->user();

        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($authUser->id == $userId) {
            return response()->json(['error' => 'Invalid unfollow action.'], 400);
        }

        // Use the `isFollowing` helper method in User model
        if (!$authUser->isFollowing($user)) {
            return response()->json(['message' => 'You are not following this user.'], 400);
        }

        $authUser->following()->detach($userId);  // Using the Eloquent relationship

        return response()->json(['message' => 'Unfollowed successfully.']);
    }

    public function unfollow_OLD(Request $request, $userId)
    {
        $authUser = auth()->user();

        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($authUser->id == $userId) {
            return response()->json(['error' => 'Invalid unfollow action.'], 400);
        }

        $deleted = Follower::where('follower_id', $authUser->id)
            ->where('following_id', $userId)
            ->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'You are not following this user.'], 400);
        }

        return response()->json(['message' => 'Unfollowed successfully.']);
    }

    public function getFollowers($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $followers = $user->followers()->select('id', 'name', 'uuid', 'profile_photo_path')->get();

        return response()->json([
            'count' => $followers->count(),
            'followers' => $followers
        ]);
    }


    public function getFollowers_OLD($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $followers = Follower::where('following_id', $userId)
            ->with('follower:id,name,uuid,profile_photo_path')
            ->get();

        if ($followers->isEmpty()) {
            return response()->json([
                'count' => 0,
                'followers' => []
            ]);
        }

        return response()->json([
            'count' => $followers->count(),
            'followers' => $followers
        ]);
    }

    public function getFollowing($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $following = $user->following()->select('id', 'name', 'uuid', 'profile_photo_path')->get();

        return response()->json([
            'count' => $following->count(),
            'following' => $following
        ]);
    }

    public function getFollowing_OLD($userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $following = Follower::where('follower_id', $userId)
                ->with('following:id,name,uuid,profile_photo_path')
                ->get();

            if ($following->isEmpty()) {
                return response()->json([
                    'count' => 0,
                    'following' => []
                ]);
            }

            return response()->json([
                'count' => $following->count(),
                'following' => $following
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
