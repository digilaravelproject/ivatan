<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\UserPost;
use App\Models\User;
use App\Services\UserPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ExclusiveContentController extends Controller
{
    public function __construct(
        private UserPostService $postService
    ) {}

    private function authUser(): User
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }

    /**
     * Store new exclusive content.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $user = $this->authUser();
            
            // Check if user is an approved exclusive creator
            $enablement = $user->enablement;
            if (!$enablement || $enablement->status !== 'approved') {
                return response()->json(['message' => 'You do not have exclusive content feature enabled.'], 403);
            }

            $price = $request->input('price');
            $isExclusive = $price !== null && (float) $price > 0;

            if (!$isExclusive) {
                return response()->json(['message' => 'Price is required and must be greater than 0 for exclusive content.'], 400);
            }

            $post = $this->postService->createPost(
                $user,
                $request->only(['type', 'caption', 'visibility']),
                $request->file('media'),
                [
                    'is_exclusive' => true,
                    'price' => $price,
                    'exclusive_status' => 'pending',
                ]
            );

            return response()->json([
                'message' => 'Exclusive Post created successfully and is pending review.',
                'data' => new PostResource($post->load('media', 'user')),
            ], 201);
        } catch (\Exception $e) {
            Log::error("Store Exclusive Post Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to create exclusive post.'], 500);
        }
    }

    /**
     * Update price for existing content (triggers pending review).
     */
    public function updatePrice(Request $request, UserPost $post): JsonResponse
    {
        try {
            $user = $this->authUser();

            if ($post->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }

            // Check if user is an approved exclusive creator
            $enablement = $user->enablement;
            if (!$enablement || $enablement->status !== 'approved') {
                return response()->json(['message' => 'You do not have exclusive content feature enabled.'], 403);
            }

            $request->validate([
                'price' => 'required|numeric|min:0',
            ]);

            $price = $request->input('price');
            $isExclusive = (float) $price > 0;

            DB::transaction(function () use ($post, $price, $isExclusive) {
                if ((float) $post->price !== (float) $price) {
                    $post->price = $isExclusive ? $price : null;
                    $post->is_exclusive = $isExclusive;
                    $post->save();
                    // Note: Observers handle the `exclusive_status` update
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Post price updated successfully. Content is pending verification if exclusive.',
                'data' => new PostResource($post->fresh()),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Update Price Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
}
