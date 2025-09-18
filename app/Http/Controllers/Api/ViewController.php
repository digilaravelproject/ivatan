<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jobs\UserJobPost;
use App\Models\UserPost;
use App\Models\UserStory;
use App\Services\ViewTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ViewController extends Controller
{
    protected $service;

    public function __construct(ViewTrackingService $service)
    {
        $this->service = $service;
    }


    public function track(Request $request, string $type, int $id)
    {
        try {
            // Map type → model
            $map = [
                'post'   => UserPost::class,
                'job'    => UserJobPost::class,
                'story'  => UserStory::class,
            ];

            if (!array_key_exists($type, $map)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type.',
                ], 422);
            }

            $modelClass = $map[$type];
            $model = $modelClass::find($id);

            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($type) . ' not found.',
                ], 404);
            }

            $success = $this->service->track($model, $request);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'View recorded.' : 'Duplicate view ignored.',
                'views_count' => $model->views()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error("Generic view tracking failed: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function trackPost(Request $request, UserPost $post)
    {
        try {
            $success = $this->service->track($post, $request);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'View recorded.' : 'Duplicate view ignored.',
                'views_count' => $post->views()->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error tracking view: ' . $e->getMessage(),
            ], 500);
        }
    }
}
