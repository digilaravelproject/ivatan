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
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Jobs\ProcessMediaJob;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserPostController extends Controller
{
    use AuthorizesRequests;
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = auth()->user();
            $posts = UserPost::with(['user', 'media'])
                ->visiblePosts($user)  // Visibility trait for posts
                ->latest()
                ->paginate(20);

            return PostResource::collection($posts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching posts.'], 500);
        }
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            // Generate UUID
            $post = DB::transaction(function () use ($request) {
                $uuid = Str::uuid();
                $post = UserPost::create([
                    'user_id' => Auth::id(),
                    'uuid' => $uuid,
                    'type' => $request->type,
                    'caption' => $request->caption,
                    'visibility' => $request->visibility,
                ]);
                $mediaFiles = $request->file('media');
                if ($mediaFiles) {
                    // Normalize to array
                    if (!is_array($mediaFiles)) {
                        $mediaFiles = [$mediaFiles];
                    }

                    foreach ($mediaFiles as $file) {
                        // Determine collection name
                        $collection = str_starts_with($file->getClientMimeType(), 'image/')
                            ? 'images'
                            : 'videos';

                        // Add to media collection (this returns a Media model)
                        $media = $post->addMedia($file)->toMediaCollection($collection);

                        // ✅ Ensure $media is a Media model before dispatching the job
                        if ($media instanceof Media) {
                            ProcessMediaJob::dispatch($media);
                        }
                    }
                }

                return $post;
            });
            return response()->json([
                'message' => 'Post created',
                'data' => $post->load('media', 'user'),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(UserPost $post): JsonResponse
    {
        try {
            // Check if the user is authorized to view the post
            $this->authorize('view', $post);

            // If authorized, return the post with associated media and user data
            return response()->json($post->load('media', 'user'));
        } catch (AuthorizationException $e) {
            // If authorization fails, return a 404 Not Found response
            return response()->json(['message' => 'Post not found.'], 404);
        }
    }

    public function destroy(UserPost $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }
    public function like($id, LikeService $likeService): JsonResponse
    {
        try {
            $post = UserPost::findOrFail($id);
            $likeService->like($post);

            return response()->json([
                'message' => 'Post liked successfully.',
                'likes_count' => $likeService->likeCount($post)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function unlike($id, LikeService $likeService): JsonResponse
    {
        try {
            $post = UserPost::findOrFail($id);
            $likeService->unlike($post);

            return response()->json([
                'message' => 'Post unliked successfully.',
                'likes_count' => $likeService->likeCount($post)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function reels(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = auth()->user();
            $reels = UserPost::with(['user', 'media'])
                ->visibleReels($user)  // Visibility trait for reels
                ->latest()
                ->paginate(20);

            return PostResource::collection($reels);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching reels.'], 500);
        }
    }
}
