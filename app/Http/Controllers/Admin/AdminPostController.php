<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Comment; // Assuming you have a Comment model for comments
use App\Models\Like;    // Assuming you have a Like model for likes
use Illuminate\Support\Facades\DB;

class AdminPostController extends Controller
{
    public function index(Request $request)
    {
        $posts = UserPost::with(['user', 'media'])
            ->withCount(['likes', 'comments'])
            ->active() // status = 'active'
            ->latest()
            ->paginate(20);


        // return response()->json($posts);
        return view('admin.post.index', compact('posts'));
    }

    public function show($postId): JsonResponse
    {
        // Fetching a specific post with detailed information
        $post = UserPost::with(['user', 'media', 'comments', 'likes'])  // Including likes and comments relationship
            ->where('id', $postId)
            ->first();

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $postDetails = [
            'post' => $post,
            'likes_count' => $post->likes->count(), // Counting likes for this post
            'comments_count' => $post->comments->count(), // Counting comments for this post
        ];

        return response()->json($postDetails);
    }
}
