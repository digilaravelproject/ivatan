<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreStoryRequest;
use App\Http\Resources\StoryFeedResource;
use App\Http\Resources\StoryResource;
use App\Jobs\GenerateThumbnailJob;
use App\Models\User;
use App\Models\UserStory;
use App\Models\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryController extends Controller
{
    /**
     * Get Active Stories Grouped by User (Instagram Feed Style).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 30);
            $authUser = Auth::user();

            // Fetch Users who have active stories
            // Optimize: Eager load stories and their media/likes/views
            $usersWithStories = User::whereHas('stories', function ($query) {
                $query->active();
            })
                ->with(['stories' => function ($query) use ($authUser) {
                    $query->active()
                        ->orderBy('created_at', 'asc') // Chronological order
                        ->with(['media', 'views', 'likes']); // Eager load relations
                }])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => StoryFeedResource::collection($usersWithStories),
                'pagination' => [
                    'current_page' => $usersWithStories->currentPage(),
                    'total_pages' => $usersWithStories->lastPage(),
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error("Story Feed Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to load stories.'], 500);
        }
    }

    /**
     * Create a New Story.
     */
    public function store(StoreStoryRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Logic for expiration
            $expiresAt = $request->filled('expires_at')
                ? now()->parse($request->input('expires_at'))
                : now()->addHours(24);

            $mimeType = $request->file('media')->getMimeType();
            $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

            $story = UserStory::create([
                'user_id' => $user->id,
                'caption' => $request->caption,
                'meta' => $request->meta,
                'type' => $type,
                'expires_at' => $expiresAt,
            ]);

            $media = $story->addMedia($request->file('media'))->toMediaCollection('stories');

            // Optional: Generate thumbnail for video in background
            if ($type === 'video') {
                dispatch(new GenerateThumbnailJob($story));
            }

            Cache::forget("user:{$user->id}:stories");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Story uploaded successfully.',
                'data' => new StoryResource($story->load('media'))
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Story Upload Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Upload failed.'], 500);
        }
    }

    /**
     * Mark a story as viewed.
     */
    public function markAsViewed(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $story = UserStory::findOrFail($id);

            // Prevent duplicate view recording for this session/user
            $existingView = $story->views()
                ->where('user_id', $user->id)
                ->exists();

            if (!$existingView) {
                $story->views()->create([
                    'user_id' => $user->id,
                    'ip_address' => request()->ip()
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Toggle Like.
     */
    public function toggleLike(int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $story = UserStory::active()->findOrFail($id);

            $hasLiked = $story->likes()->where('user_id', $user->id)->exists();

            if ($hasLiked) {
                $story->likes()->detach($user->id);
                $story->decrement('like_count');
                $message = 'Unliked';
            } else {
                $story->likes()->attach($user->id);
                $story->increment('like_count');
                $message = 'Liked';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'like_count' => $story->fresh()->like_count
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Action failed.'], 500);
        }
    }

    /**
     * Delete a Story.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $story = UserStory::where('user_id', Auth::id())->findOrFail($id);
            $story->delete();

            return response()->json(['success' => true, 'message' => 'Story deleted.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }
}
