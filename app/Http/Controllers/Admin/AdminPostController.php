<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Comment; // Assuming you have a Comment model for comments
use App\Models\Like;    // Assuming you have a Like model for likes
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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



    public function show($postId)
    {
        // Fetch the post with related user, media, comments, and likes
        // $post = UserPost::with(['user', 'media', 'comments', 'likes'])->find($postId);
$post = UserPost::with(['user', 'media', 'comments', 'likes'])
    ->withCount(['likes', 'comments'])
    ->find($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // Define statuses and visibilities (can be moved to config if needed)
        $statuses = ['active', 'inactive', 'pending'];
        $visibilities = ['public', 'private'];

        // return response()->json($postDetails);
        return view('admin.post.details', [
            'post' => $post,
            'statuses' => $statuses,
            'visibilities' => $visibilities
        ]);
    }
}
