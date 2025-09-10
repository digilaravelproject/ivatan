<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\UserPost;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Jobs\ProcessMediaJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserPostController extends Controller
{

    public function index()
    {
        $posts = UserPost::with(['user', 'media'])->latest()->paginate(20);
        // return response()->json($posts);
        return PostResource::collection($posts);
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
        return response()->json($post->load('media', 'user'));
    }

    public function destroy(UserPost $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }
    public function like($id, LikeService $likeService)
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

    public function unlike($id, LikeService $likeService)
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

    public function reels()
{
    $reels = UserPost::where('type', 'reel')
        ->with('media','user')
        ->latest()
        ->paginate(20);

    return PostResource::collection($reels);
}

}
