<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Jobs\ProcessMediaJob;
use App\Models\UserPost;
use App\Services\LikeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserPostController extends Controller
{
    use AuthorizesRequests;

    /**
     * 1. MIXED FEED (Home/Explore)
     * Shows everything (Posts, Videos, Reels) shuffled randomly.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = auth()->user();
            $posts = UserPost::with(['user', 'media'])
                ->visiblePosts($user) // Ensure you have this scope in your Model
                ->inRandomOrder()     // ✅ "Algorithm" style shuffle
                ->paginate(20);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the feed.'], 500);
        }
    }

    /**
     * 2. IMAGE/TEXT POSTS FEED
     * Only shows standard posts (Images or Text only).
     */
    public function postsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = auth()->user();
            $posts = UserPost::with(['user', 'media'])
                ->visiblePosts($user)
                ->where('type', 'post') // Ensure it's a standard post
                ->where(function ($q) {
                    // Has Images OR has No Media (Text only)
                    $q->whereHas('media', function ($m) {
                        $m->where('mime_type', 'like', 'image/%');
                    })->orWhereDoesntHave('media');
                })
                ->inRandomOrder() // ✅ Shuffle
                ->paginate(20);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching posts.'], 500);
        }
    }

    /**
     * 3. VIDEO FEED (Watch)
     * Only shows standard videos (Not Reels).
     */
    public function videosFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = auth()->user();
            $videos = UserPost::with(['user', 'media'])
                ->visiblePosts($user)
                ->where('type', 'post') // Standard post type
                ->whereHas('media', function ($q) {
                    $q->where('mime_type', 'like', 'video/%'); // Must be video
                })
                ->inRandomOrder() // ✅ Shuffle
                ->paginate(15);

            return PostResource::collection($videos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching videos.'], 500);
        }
    }

    /**
     * 4. REELS FEED
     * Only shows Reels (Short vertical videos).
     */
    public function reelsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = auth()->user();
            // visibleReels scope should handle specific logic for reels if needed
            $reels = UserPost::with(['user', 'media'])
                ->where('type', 'reel')
                // ->visibleReels($user) // Use if you have specific visibility logic for reels
                ->visiblePosts($user)    // Fallback to standard visibility
                ->inRandomOrder()        // ✅ Shuffle (Algorithm)
                ->paginate(10);

            return PostResource::collection($reels);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching reels.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // CRUD ACTIONS
    // -------------------------------------------------------------------------

    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $post = DB::transaction(function () use ($request) {
                $uuid = Str::uuid();
                $post = UserPost::create([
                    'user_id' => Auth::id(),
                    'uuid' => $uuid,
                    'type' => $request->type, // 'post' or 'reel'
                    'caption' => $request->caption,
                    'visibility' => $request->visibility,
                ]);

                $mediaFiles = $request->file('media');
                if ($mediaFiles) {
                    if (! is_array($mediaFiles)) {
                        $mediaFiles = [$mediaFiles];
                    }

                    foreach ($mediaFiles as $file) {
                        $collection = str_starts_with($file->getClientMimeType(), 'image/')
                            ? 'images'
                            : 'videos';

                        $media = $post->addMedia($file)->toMediaCollection($collection);

                        if ($media instanceof Media) {
                            ProcessMediaJob::dispatch($media);
                        }
                    }
                }

                return $post;
            });

            return response()->json([
                'message' => 'Post created successfully',
                'data' => new PostResource($post->load('media', 'user')),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(UserPost $post): JsonResponse
    {
        try {
            $this->authorize('view', $post);

            return response()->json(new PostResource($post->load('media', 'user')));
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Post not found or private.'], 404);
        }
    }

    public function destroy(UserPost $post): JsonResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    // -------------------------------------------------------------------------
    // INTERACTIONS (Like/Unlike)
    // -------------------------------------------------------------------------

    /**
     * ✅ TOGGLE LIKE
     * Checks if liked: if yes -> unlike, if no -> like.
     */
    public function toggleLike($id, LikeService $likeService): JsonResponse
    {
        try {
            $post = UserPost::findOrFail($id);
            $user = auth()->user();

            // Check if already liked using the Service or Relation
            // Assuming LikeService has an `isLikedBy` or similar check,
            // Or we check relationship directly:
            $hasLiked = $post->likes()->where('user_id', $user->id)->exists();

            if ($hasLiked) {
                // Perform Unlike
                $likeService->unlike($post);
                $message = 'Post unliked.';
                $isLiked = false;
            } else {
                // Perform Like
                $likeService->like($post);
                $message = 'Post liked.';
                $isLiked = true;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'is_liked' => $isLiked,
                    'likes_count' => $likeService->likeCount($post),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
