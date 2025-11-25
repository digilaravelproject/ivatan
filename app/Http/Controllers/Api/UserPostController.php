<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\UserPost;
use App\Services\LikeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Jobs\ProcessMediaJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class UserPostController
 * Handles all logic regarding Posts, Reels, and Videos with Algorithmic Feeds.
 * @package App\Http\Controllers\Api
 */
class UserPostController extends Controller
{
    /**
     * Helper to get authenticated user with correct type hinting.
     * Fixes: Intelephense(P1013)
     */
    private function getAuthUser(): \App\Models\User
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user;
    }

    /**
     * 1. MIXED FEED (Home/Explore) - "For You" Logic
     * Algorithm:
     * - Shows active posts.
     * - Mixes popular content (High Views/Likes/Comments) with fresh content.
     * - Adds randomness so the feed doesn't look static.
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $posts = UserPost::query()
                ->active() // Only Active posts
                ->with(['user', 'media'])
                // 'forYou' scope uses: (Views + Likes*5 + Comments*10) logic + Randomness
                ->forYou()
                ->paginate(20);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            Log::error("Feed Fetch Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the feed.'], 500);
        }
    }

    /**
     * 2. POSTS FEED (Images/Text/Carousel)
     * Algorithm: Trending First (High Engagement)
     * Excludes Videos and Reels.
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function postsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $posts = UserPost::query()
                ->active()
                ->whereIn('type', ['post', 'carousel', 'video'])
                ->with(['user', 'media'])
                // 'trending' sorts purely by Engagement Score (Highest first)
                ->trending()
                ->paginate(20);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            Log::error("Posts Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching posts.'], 500);
        }
    }

    /**
     * 3. VIDEO FEED
     * Algorithm: Viral Videos (High Watch/Like Count)
     * Shows only Long Videos.
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function videosFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $videos = UserPost::query()
                ->active()
                ->ofType('video')
                ->with(['user', 'media'])
                // Viral videos appear at the top
                ->trending()
                ->paginate(15);

            return PostResource::collection($videos);
        } catch (\Exception $e) {
            Log::error("Videos Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching videos.'], 500);
        }
    }

    /**
     * 4. REELS FEED
     * Algorithm: Viral Reels
     * Shows only Reels sorted by popularity.
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function reelsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $reels = UserPost::query()
                ->active()
                ->ofType('reel')
                ->with(['user', 'media'])
                // Viral reels logic
                ->trending()
                ->paginate(10);

            return PostResource::collection($reels);
        } catch (\Exception $e) {
            Log::error("Reels Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching reels.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // CRUD ACTIONS
    // -------------------------------------------------------------------------

    /**
     * Store a newly created resource in storage.
     * @param StorePostRequest $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $user = $this->getAuthUser();

            $post = DB::transaction(function () use ($request, $user) {
                $uuid = Str::uuid();

                // Create Post
                $post = UserPost::create([
                    'user_id'    => $user->id,
                    'uuid'       => $uuid,
                    'type'       => $request->type,
                    'caption'    => $request->caption,
                    'visibility' => $request->visibility ?? 'public',
                    'status'     => 'active',
                    'view_count' => 0, // Initialize counts for algorithm
                    'like_count' => 0,
                    'comment_count' => 0,
                ]);

                // Handle Media Uploads
                if ($request->hasFile('media')) {
                    $mediaFiles = $request->file('media');

                    if (!is_array($mediaFiles)) {
                        $mediaFiles = [$mediaFiles];
                    }

                    foreach ($mediaFiles as $file) {
                        $mimeType = $file->getClientMimeType();
                        $collection = str_starts_with($mimeType, 'image/') ? 'images' : 'videos';

                        $media = $post->addMedia($file)->toMediaCollection($collection);

                        if ($media instanceof Media) {
                            try {
                                ProcessMediaJob::dispatch($media);
                            } catch (\Exception $e) {
                                Log::error("Job Dispatch Failed: " . $e->getMessage());
                            }
                        }
                    }
                }
                return $post;
            });

            return response()->json([
                'message' => 'Post created successfully',
                'data'    => new PostResource($post->load('media', 'user')),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // 🔴 CRITICAL: Logging the actual error
            Log::error("Store Post Error: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            // 🔴 CRITICAL: Returning the actual error to Postman/Frontend
            return response()->json([
                'error' => 'Failed to create post.',
                'debug_message' => $e->getMessage(), // Look at this field in Postman
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * @param UserPost $post
     * @return JsonResponse
     */
    public function show(UserPost $post): JsonResponse
    {
        try {
            // Increment view count for the algorithm when opened
            $post->increment('view_count');

            // Optional: Authorization check
            // $this->authorize('view', $post);

            return response()->json(new PostResource($post->load('media', 'user')));
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'You are not authorized to view this post.'], 403);
        } catch (\Exception $e) {
            Log::error("Show Post Error: " . $e->getMessage());
            return response()->json(['message' => 'Post not found.'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param UserPost $post
     * @return JsonResponse
     */
    public function destroy(UserPost $post): JsonResponse
    {
        try {
            // $this->authorize('delete', $post); // Uncommented for security
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully']);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        } catch (\Exception $e) {
            Log::error("Delete Post Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete post.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // INTERACTIONS
    // -------------------------------------------------------------------------

    /**
     * Toggle like status for a post.
     * @param int $id
     * @param LikeService $likeService
     * @return JsonResponse
     */
    public function toggleLike($id, LikeService $likeService): JsonResponse
    {
        try {
            $post = UserPost::findOrFail($id);
            $user = $this->getAuthUser();

            $hasLiked = $post->likes()->where('user_id', $user->id)->exists();

            if ($hasLiked) {
                $likeService->unlike($post);
                // Decrement count manually if LikeService doesn't handle it automatically
                // $post->decrement('like_count');
                $message = 'Post unliked.';
                $isLiked = false;
            } else {
                $likeService->like($post);
                // Increment count manually if LikeService doesn't handle it automatically
                // $post->increment('like_count');
                $message = 'Post liked.';
                $isLiked = true;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => [
                    'is_liked'    => $isLiked,
                    'likes_count' => $likeService->likeCount($post)
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Toggle Like Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 400);
        }
    }
}
