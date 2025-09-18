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
        try {
            $type = $request->get('type', 'post'); // Default to 'post'

            $posts = UserPost::with(['user', 'media'])
                ->withCount(['likes', 'comments'])
                ->where('type', $type)
                ->active()
                ->latest()
                ->paginate(20);


            return view('admin.post.index', compact('posts'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error fetching posts: ' . $e->getMessage());

            // Option 1: Return back with error flash message
            return redirect()->back()->withErrors('Failed to load posts. Please try again.');

            // Option 2: Or return a custom error view
            // return view('errors.custom', ['message' => 'Failed to load posts. Please try again later.']);
        }
    }





    public function show($postId)
    {
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
