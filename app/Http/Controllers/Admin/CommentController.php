<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Services\CommentService;

class CommentController extends Controller
{
    public function destroy(Comment $comment, CommentService $commentService)
    {
        $commentService->deleteComment($comment);

        // return response()->json(['message' => 'Comment deleted']);
        return back()->with('success', 'Comment deleted successfully.');
    }
}
