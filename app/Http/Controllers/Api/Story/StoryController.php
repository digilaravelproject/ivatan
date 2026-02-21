<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreStoryRequest;
use App\Http\Resources\StoryFeedResource;
use App\Http\Resources\StoryResource;
use App\Services\StoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function __construct(protected StoryService $storyService) {}

public function index(Request $request): JsonResponse
    {
        try {
            // Service se SimplePaginator data aayega
            $feed = $this->storyService->getFeed(Auth::user(), $request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => StoryFeedResource::collection($feed),
                // [FIX] Meta data change kiya hai Simple Pagination ke liye
                'meta' => [
                    'current_page' => $feed->currentPage(),
                    'has_more' => $feed->hasMorePages(),
                    'per_page' => $feed->perPage(),
                    'next_page_url' => $feed->nextPageUrl(),
                    'prev_page_url' => $feed->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            // Error dekhne ke liye message return kar rahe hain temporary
            return response()->json([
                'success' => false, 
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myStories(): JsonResponse
    {
        $result = $this->storyService->getUserStories(Auth::user(), Auth::user()->username);
        return response()->json([
            'success' => true,
            'data' => StoryResource::collection($result['stories'])
        ]);
    }

    public function getStoriesByUsername(string $username): JsonResponse
    {
        $result = $this->storyService->getUserStories(Auth::user(), $username);

        if ($result['status'] === 'private') {
            return response()->json(['success' => false, 'message' => 'Private Account.'], 403);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $result['user']->name,
                'username' => $result['user']->username,
                'avatar' => $result['user']->profile_photo_url,
                'is_mine' => Auth::id() === $result['user']->id,
                'is_verified' => $result['user']->is_verified ?? false
            ],
            'data' => StoryResource::collection($result['stories'])
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $story = $this->storyService->getStoryById(Auth::user(), $id);
        if (!$story) {
            return response()->json(['success' => false, 'message' => 'Story not found or private.'], 404);
        }
        return response()->json(['success' => true, 'data' => new StoryResource($story)]);
    }

    public function store(StoreStoryRequest $request): JsonResponse
    {
        try {
            $story = $this->storyService->createStory(
                Auth::user(),
                $request->validated(),
                $request->file('media')
            );

            return response()->json([
                'success' => true,
                'message' => 'Story uploaded.',
                'data' => new StoryResource($story->load('media'))
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed.'], 500);
        }
    }

    public function markAsViewed(int $id): JsonResponse
    {
        $this->storyService->markAsViewed(Auth::user(), $id);
        return response()->json(['success' => true]);
    }

    public function toggleLike(int $id): JsonResponse
    {
        try {
            $data = $this->storyService->toggleLike(Auth::user(), $id);
            return response()->json([
                'success' => true,
                'message' => $data['is_liked'] ? 'Liked' : 'Unliked',
                'like_count' => $data['count'],
                'is_liked' => $data['is_liked']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Action failed.'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->storyService->deleteStory(Auth::user(), $id);
        return $deleted
            ? response()->json(['success' => true, 'message' => 'Deleted.'])
            : response()->json(['success' => false, 'message' => 'Unauthorized or not found.'], 403);
    }
}
