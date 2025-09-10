<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\UserPost;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CommentService;
use Exception;

class CommentController extends Controller
{

    /**
     * ✅ List comments with replies on a post/reel/etc.
     * Get all top-level comments for a specific model (e.g., Post, Video).
     */
    // public function index(string $commentableType, int $commentableId): JsonResponse
    // {
    //     $modelClass = 'App\\Models\\' . $commentableType;

    //     if (!class_exists($modelClass)) {
    //         return response()->json(['error' => 'Invalid commentable type.'], 400);
    //     }

    //     $model = $modelClass::findOrFail($commentableId);

    //     $comments = $model->comments()
    //         ->whereNull('parent_id')
    //         ->with(['user', 'replies.user', 'likes', 'replies.likes'])
    //         ->latest()
    //         ->get();
    //     if (!$model) {
    //         return response()->json(['error' => 'Resource not found.'], 404);
    //     }
    //     return response()->json($comments);
    // }

    public function postComments(UserPost $post): JsonResponse
    {

        return $this->getCommentsFor($post);
    }

    // public function videoComments(Video $video): JsonResponse
    // {
    //     return $this->getCommentsFor($video);
    // }

    // public function productComments(Product $product): JsonResponse
    // {
    //     return $this->getCommentsFor($product);
    // }

    protected function getCommentsFor($model): JsonResponse
    {
        $comments = $model->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'likes', 'replies.likes'])
            ->latest()
            ->get();

        return response()->json($comments);
    }

    // ✅ Store comment or reply
    public function store(StoreCommentRequest $request, CommentService $commentService): JsonResponse
    {
        $comment = $commentService->addComment($request->validated());


        return response()->json([
            'message' => 'Comment posted',
            'data' => $comment->load('user', 'replies', 'likes')
        ], 201);
    }


    // ✅ Like / Unlike a comment
    // public function toggleLike(Comment $comment): JsonResponse
    // {
    //     $user = Auth::user();

    //     $like = Like::where([
    //         'user_id' => $user->id,
    //         'likeable_type' => Comment::class,
    //         'likeable_id' => $comment->id,
    //     ])->first();

    //     if ($like) {
    //         $like->delete();
    //         $liked = false;
    //     } else {
    //         $comment->likes()->create(['user_id' => $user->id]);
    //         $liked = true;
    //     }

    //     return response()->json([
    //         'liked' => $liked,
    //         'like_count' => $comment->likes()->count()
    //     ]);
    // }


    public function toggleCommentLike(Comment $comment, LikeService $likeService): JsonResponse
    {
        $result = $likeService->like($comment);

        return response()->json([
            'message' => $result['liked'] ? 'Liked' : 'Unliked',
            'liked' => $result['liked'],
            'likes_count' => $result['likes_count'],
        ]);
    }

    // ✅ Delete a comment
    public function destroy(Comment $comment, CommentService $commentService): JsonResponse
    {
        $this->authorize('delete', $comment); // Optional: Restrict ownership

        $commentService->deleteComment($comment);

        return response()->json(['message' => 'Comment deleted']);
    }
}
