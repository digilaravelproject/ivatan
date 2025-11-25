<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreStoryRequest;
use App\Http\Resources\StoryResource;
use App\Jobs\GenerateThumbnailJob;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StoryController extends Controller
{
    /**
     * Get All Active Stories (Feed)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);

            // Fetch active stories with eager loading
            $stories = UserStory::with(['user', 'media', 'likes']) // Eager load likes for 'liked_by_me' check
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => StoryResource::collection($stories),
                'pagination' => [
                    'current_page' => $stories->currentPage(),
                    'total_pages' => $stories->lastPage(),
                    'total_items' => $stories->total(),
                    'per_page' => $stories->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Story Index Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to load stories.',
                'error' => $e->getMessage() // Dev mode me dikhana, production me hide karna
            ], 500);
        }
    }

    /**
     * Create a New Story
     */
    public function store(StoreStoryRequest $request): JsonResponse
    {
        DB::beginTransaction(); // Start Transaction

        try {
            $user = Auth::user();
            $expiresAt = $request->input('expires_at')
                ? now()->parse($request->input('expires_at'))
                : now()->addHours(24);

            // Determine Type
            $mimeType = $request->file('media')->getMimeType();
            $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

            // Create Story Record
            $story = UserStory::create([
                'user_id' => $user->id,
                'caption' => $request->caption,
                'meta' => $request->meta,
                'type' => $type,
                'expires_at' => $expiresAt,
                'like_count' => 0
            ]);

            // Handle Media Upload using Spatie
            $media = $story->addMedia($request->file('media'))
                ->toMediaCollection('stories');

            // Custom Properties save karna
            if ($request->filled('caption') || $request->filled('meta')) {
                $media->setCustomProperty('caption', $request->caption);
                $media->setCustomProperty('meta', $request->meta ?? []);
                $media->save();
            }

            // Thumbnail Job (Video ke liye)
            if ($type === 'video') {
                // Ensure Job class exists and works
                dispatch(new GenerateThumbnailJob($story));
            }

            // Clear Cache
            Cache::forget("user:{$user->id}:stories");

            DB::commit(); // Save everything

            return response()->json([
                'success' => true,
                'message' => 'Story uploaded successfully.',
                'data' => new StoryResource($story->load('user', 'media'))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Revert changes if error occurs
            Log::error("Story Create Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload story.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Single Story
     */
    public function show($id): JsonResponse
    {
        try {
            // Find story or fail
            $story = UserStory::with(['user', 'media'])->findOrFail($id);

            // Check Expiry
            if ($story->expires_at->lte(now())) {
                return response()->json(['success' => false, 'message' => 'This story has expired.'], 410); // 410 Gone
            }

            return response()->json([
                'success' => true,
                'data' => new StoryResource($story)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Story not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Story Show Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error.'], 500);
        }
    }

    /**
     * Get Stories by Username
     */
    public function getUserStories(Request $request, $username): JsonResponse
    {
        try {
            $user = User::where('username', $username)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => "User {$username} not found."], 404);
            }

            $perPage = $request->get('per_page', 10);

            // Active stories only
            $stories = UserStory::with(['media', 'user'])
                ->where('user_id', $user->id)
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => StoryResource::collection($stories),
                'pagination' => [
                    'current_page' => $stories->currentPage(),
                    'total_pages' => $stories->lastPage(),
                    'per_page' => $stories->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("User Stories Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error.'], 500);
        }
    }

    /**
     * Like a Story
     */
    public function like($storyId): JsonResponse
    {
        try {
            $user = Auth::user();
            $story = UserStory::findOrFail($storyId);

            if ($story->expires_at <= now()) {
                return response()->json(['success' => false, 'message' => 'Cannot like an expired story.'], 400);
            }

            // Check if already liked
            if ($story->likes()->where('user_id', $user->id)->exists()) {
                return response()->json(['success' => true, 'message' => 'Already liked.', 'like_count' => $story->like_count]);
            }

            // Attach Like
            $story->likes()->attach($user->id);
            $story->increment('like_count');

            return response()->json([
                'success' => true,
                'message' => 'Story liked.',
                'like_count' => $story->fresh()->like_count
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Story not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Like Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Action failed.'], 500);
        }
    }

    /**
     * Unlike a Story
     */
    public function unlike($storyId): JsonResponse
    {
        try {
            $user = Auth::user();
            $story = UserStory::findOrFail($storyId);

            // Check if liked
            if (!$story->likes()->where('user_id', $user->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'You have not liked this story.'], 400);
            }

            // Detach Like
            $story->likes()->detach($user->id);

            // Safe decrement
            if ($story->like_count > 0) {
                $story->decrement('like_count');
            }

            return response()->json([
                'success' => true,
                'message' => 'Story unliked.',
                'like_count' => $story->fresh()->like_count
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Story not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Unlike Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Action failed.'], 500);
        }
    }

    /**
     * My Stories (Logged in user)
     */
    public function myStories(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // Active Stories
            $liveStories = UserStory::with(['media', 'user'])
                ->where('user_id', $user->id)
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->get();

            // Archived Stories (Expired)
            $archivedStories = UserStory::with(['media', 'user'])
                ->where('user_id', $user->id)
                ->where('expires_at', '<=', now())
                ->orderBy('created_at', 'desc')
                ->take(20) // Limit archive fetch
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'live' => StoryResource::collection($liveStories),
                    'archived' => StoryResource::collection($archivedStories),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("My Stories Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not fetch your stories.'], 500);
        }
    }
}
