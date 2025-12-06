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
use App\Models\User;
use App\Services\ViewTrackingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserPostController extends Controller
{
    /**
     * Get the authenticated user.
     */
    private function getAuthUser(): \App\Models\User
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user;
    }

    /**
     * Mixed Feed (Home/Explore) - "For You" Logic.
     * Shows a mix of active posts based on engagement and randomness.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $posts = UserPost::query()
                ->active()
                ->with(['user.interests', 'media'])
                ->forYou() // Algorithmic scope
                ->paginate(20);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            Log::error("Feed Fetch Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the feed.'], 500);
        }
    }

    /**
     * Posts Feed.
     * Shows Images, Text, and Carousels sorted by trending score.
     */
    public function postsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $posts = UserPost::query()
                ->active()
                ->whereIn('type', ['post', 'carousel', 'video'])
                ->with(['user.interests', 'media'])
                ->trending()
                ->paginate(20);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            Log::error("Posts Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching posts.'], 500);
        }
    }

    /**
     * Videos Feed.
     * Shows only long-form videos sorted by engagement.
     */
    public function videosFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $videos = UserPost::query()
                ->active()
                ->ofType('video')
                ->with(['user.interests', 'media'])
                ->trending()
                ->paginate(15);

            return PostResource::collection($videos);
        } catch (\Exception $e) {
            Log::error("Videos Feed Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching videos.'], 500);
        }
    }

    /**
     * Reels Feed.
     * Shows short videos (reels) sorted by popularity.
     */
    public function reelsFeed(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $reels = UserPost::query()
                ->active()
                ->ofType('reel')
                ->with(['user.interests', 'media'])
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
     * Create a new post and handle media uploads.
     * Automatically sorts files into 'images' or 'videos' collections.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $user = $this->getAuthUser();

            $post = DB::transaction(function () use ($request, $user) {
                $uuid = Str::uuid();

                // Create Post Record
                $post = UserPost::create([
                    'user_id'    => $user->id,
                    'uuid'       => $uuid,
                    'type'       => $request->type,
                    'caption'    => $request->caption,
                    'visibility' => $request->visibility ?? 'public',
                    'status'     => 'active',
                    'view_count' => 0,
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
                        // Detect MIME type on server side for accuracy
                        $mimeType = $file->getMimeType();

                        $collection = null;

                        // Assign collection based on file content, not post type.
                        // This matches the Model's validation rules.
                        if (str_starts_with($mimeType, 'image/')) {
                            $collection = 'images';
                        } elseif (str_starts_with($mimeType, 'video/')) {
                            $collection = 'videos';
                        }

                        // Upload if valid collection found
                        if ($collection) {
                            $media = $post->addMedia($file)->toMediaCollection($collection);

                            // Trigger background processing
                            if ($media instanceof Media) {
                                try {
                                    ProcessMediaJob::dispatch($media);
                                } catch (\Exception $e) {
                                    Log::error("Media Processing Job Failed: " . $e->getMessage());
                                }
                            }
                        } else {
                            Log::warning("Skipped file upload: Unsupported MIME type {$mimeType}");
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
            Log::error("Store Post Error: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'error' => 'Failed to create post.',
                'debug_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function getPostsByUser(Request $request, $username): AnonymousResourceCollection|JsonResponse
    {
        try {
            // 1. Target User ko find karo
            $targetUser = User::where('username', $username)->first();

            if (!$targetUser) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // 2. Current Logged-in User (Check karo kaun dekh raha hai)
            /** @var \App\Models\User|null $currentUser */
            $currentUser = Auth::guard('sanctum')->user();

            // 3. Privacy Check Logic
            $isMine = $currentUser && $currentUser->id === $targetUser->id;

            // Check following status
            $isFollowing = false;
            if ($currentUser && !$isMine) {
                $isFollowing = $currentUser->isFollowing($targetUser);
            }

            // AGAR Account Private hai AND (Na main khud hu, Na main follower hu) -> ACCESS DENIED
            if ($targetUser->account_privacy === 'private' && !$isMine && !$isFollowing) {
                return response()->json([
                    'message' => 'This account is private. Follow to see their posts.',
                    'is_private' => true,
                    'posts' => []
                ], 403);
            }

            // 4. Query Build Karo
            $query = UserPost::query()
                ->where('user_id', $targetUser->id)
                ->active() // Sirf Active posts
                ->with(['media', 'user']); // Eager load relations

            // ✅ 5. APPLY FILTER LOGIC HERE
            // Frontend bhejega: ?filter=posts YA ?filter=videos YA ?filter=all
            $filter = $request->get('filter', 'all');

            if ($filter === 'posts') {
                // Filter 1: Sirf Images aur Carousels
                $query->whereIn('type', ['post', 'carousel']);
            } elseif ($filter === 'videos') {
                // Filter 2: Sirf Videos aur Reels
                $query->whereIn('type', ['video', 'reel']);
            }
            // else case 'all' hai, usme koi where clause nahi lagega (sab aayega)

            // 6. Sorting & Pagination
            $posts = $query->orderBy('created_at', 'DESC')
                ->paginate(12);

            // 7. Return Resource
            return PostResource::collection($posts)->additional([
                'meta' => [
                    'user_stats' => [
                        'post_count' => $targetUser->posts()->active()->count(), // Total count hamesha full dikhana chahiye
                        'is_private' => $targetUser->account_privacy === 'private',
                        'is_following' => $isFollowing,
                        'current_filter' => $filter // Frontend ko batao kaunsa filter laga hai
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("User Posts Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }
    /**
     * Show a single post.
     */
    public function show(Request $request, UserPost $post, ViewTrackingService $viewService): JsonResponse
    {
        try {
            // $post->increment('view_count');
            $viewService->track($post, $request);
            return response()->json(new PostResource($post->load('media', 'user')));
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'You are not authorized to view this post.'], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Post not found.'], 404);
        }
    }

    /**
     * Delete a post.
     */
    public function destroy(UserPost $post): JsonResponse
    {
        try {
            // $this->authorize('delete', $post);
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
     * Toggle Like/Unlike on a post.
     */
    public function toggleLike($id, LikeService $likeService): JsonResponse
    {
        try {
            $post = UserPost::findOrFail($id);
            $user = $this->getAuthUser();

            $hasLiked = $post->likes()->where('user_id', $user->id)->exists();

            if ($hasLiked) {
                $likeService->unlike($post);
                $message = 'Post unliked.';
                $isLiked = false;
            } else {
                $likeService->like($post);
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
        } catch (\Exception $e) {
            Log::error("Like Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 400);
        }
    }
}
