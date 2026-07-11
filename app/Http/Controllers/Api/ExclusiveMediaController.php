<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;

class ExclusiveMediaController extends Controller
{
    /**
     * Serve exclusive media securely.
     */
    public function show(Request $request, UserPost $post, $mediaId)
    {
        if (!Gate::allows('canAccessExclusiveContent', $post)) {
            return response()->json(['message' => 'Unauthorized or blocked. Purchase required.'], 403);
        }

        $media = $post->media()->findOrFail($mediaId);

        // Serve the file directly if it exists
        if (Storage::disk($media->disk)->exists($media->id . '/' . $media->file_name)) {
            return Storage::disk($media->disk)->response($media->id . '/' . $media->file_name);
        }

        return response()->json(['message' => 'Media not found.'], 404);
    }
}
