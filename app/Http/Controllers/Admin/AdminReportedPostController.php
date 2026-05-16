<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use App\Models\PostReport;
use Illuminate\Http\Request;

class AdminReportedPostController extends Controller
{
    /**
     * List all reported posts
     */
    public function index(Request $request)
    {
        $type = $request->get('type'); // post | video | reel (optional)

        $posts = UserPost::with([
                'user',
                'media',
                'reports'
            ])
            ->withCount([
                'reports',
                'likes',
                'comments'
            ])
            ->whereHas('reports') // ONLY reported posts
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate(20);

        return view('admin.reported-post.index', compact('posts'));
    }

    /**
     * Show reported post details
     */
    public function show($id)
    {
        $post = UserPost::with([
                'user',
                'media',
                'likes.user',
                'comments.user',
                'reports.user'
            ])
            ->withCount([
                'likes',
                'comments',
                'reports'
            ])
            ->findOrFail($id);

        // 🔴 DEBUG CHECK (TEMPORARY)
        // dd($post);

        return view('admin.reported-post.details', [
            'post' => $post
        ]);
    }

    /**
     * Update post status (active / flagged / deleted)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,flagged,deleted',
        ]);

        $post = UserPost::findOrFail($id);
        $post->status = $request->status;
        $post->save();

        return back()->with('success', 'Post status updated successfully.');
    }

    /**
     * Soft delete reported post
     */
    public function softDelete($id)
    {
        $post = UserPost::findOrFail($id);
        $post->delete();

        return redirect()
            ->route('admin.reported-post.index')
            ->with('success', 'Post soft deleted successfully.');
    }

    /**
     * Permanently delete reported post
     */
    public function forceDelete($id)
    {
        $post = UserPost::withTrashed()->findOrFail($id);

        $post->comments()->delete();
        $post->likes()->delete();
        $post->reports()->delete();

        $post->clearMediaCollection('images');
        $post->clearMediaCollection('videos');

        $post->forceDelete();

        return redirect()
            ->route('admin.reported-post.index')
            ->with('success', 'Post permanently deleted.');
    }

    /**
     * -------------------------------
     * Helpers
     * -------------------------------
     */

    private function formatPost(UserPost $post, bool $detailed = false): array
    {
        return [
            'post_id' => $post->id,
            'caption' => $post->caption,
            'type' => $post->type,
            'status' => $post->status,
            'visibility' => $post->visibility,
            'created_at' => $post->created_at,

            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'profile_pic' => $post->user->profile_photo_url,
            ],

            'counts' => [
                'likes' => $post->likes_count,
                'comments' => $post->comments_count,
                'reports' => $post->reports_count,
            ],

            'media' => [
                'images' => $post->getMedia('images')->map(fn ($m) => [
                    'id' => $m->id,
                    'original_url' => $m->getUrl(),
                    'thumb_url' => $m->getUrl('thumb'),
                ]),
                'videos' => $post->getMedia('videos')->map(fn ($m) => [
                    'id' => $m->id,
                    'original_url' => $m->getUrl(),
                    'thumb_url' => $m->getUrl('thumb'),
                ]),
            ],

            'reports' => $post->reports->map(fn ($report) => [
                'report_id' => $report->id,
                'reason' => $report->reason,
                'description' => $report->description,
                'reported_by' => [
                    'id' => $report->user->id,
                    'name' => $report->user->name,
                ],
                'reported_at' => $report->created_at,
            ]),

            'likes' => $detailed
                ? $post->likes->map(fn ($like) => [
                    'user_id' => $like->user_id,
                    'username' => $like->user->name,
                ])
                : [],

            'comments' => $detailed
                ? $post->comments->map(fn ($comment) => [
                    'id' => $comment->id,
                    'content' => $comment->body,
                    'user' => $comment->user->name,
                ])
                : [],
        ];
    }
}
