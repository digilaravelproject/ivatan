<?php

use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\Story\StoryController;
use App\Http\Controllers\Api\Story\StoryHighlightController;
use App\Http\Controllers\Api\UserPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;


/**
 * Public Routes (No authentication required)
 */
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


/**
 * Versioned API Routes (v1)
 */
Route::prefix('v1')->group(function () {

    /**
     * ================================
     * Authentication Required Routes
     * ================================
     */
    Route::middleware('auth:sanctum')->group(function () {

        // ================================
        // Follow/Unfollow Routes
        // ================================
        Route::prefix('follow')->group(function () {
            Route::post('/{userId}', [FollowController::class, 'follow']);
            Route::delete('/{userId}', [FollowController::class, 'unfollow']);
        });

        // ================================
        // User Follow/Followers Routes
        // ================================
        Route::prefix('user/{userId}')->group(function () {
            Route::get('/followers', [FollowController::class, 'getFollowers']);
            Route::get('/following', [FollowController::class, 'getFollowing']);
        });

        // ================================
        // Post Routes
        // ================================
        Route::prefix('posts')->controller(UserPostController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store'); // Create post
            Route::get('/{post}', 'show'); // Show post details
            Route::delete('/{post}', 'destroy'); // Delete post

            // Like/Unlike a post
            Route::post('/{id}/like', 'like');
            Route::delete('/{id}/like', 'unlike');


            // ================================
            // Reel Routes
            // ================================
            Route::get('/reels',  'reels');
        });

        // ================================
        // Comment Routes
        // ================================
        Route::prefix('comments')->group(function () {
            // ✅ View comment or reply method 1
            // Route::get('/{commentableType}/{commentableId}', [CommentController::class, 'index']);
            // Method 2
            Route::get('/{post}', [CommentController::class, 'postComments']);
            // Post a new comment or reply
            Route::post('/', [CommentController::class, 'store']);
            // Toggle like/unlike on a comment
            Route::post('/{comment}/like', [CommentController::class, 'toggleLike']);
            // Delete comment
            Route::delete('/{comment}', [CommentController::class, 'destroy']);
        });
    });


    /**
     * ================================
     * Public Story Routes (No Authentication Required)
     * ================================
     */
    Route::prefix('stories')->group(function () {
        Route::get('/', [StoryController::class, 'index']); // Get all stories
        Route::get('/{story}', [StoryController::class, 'show']); // Get single story by ID
        Route::get('/user/{userId}', [StoryController::class, 'index']); // Get stories by user
    });


    /**
     * ================================
     * Authenticated Story Routes
     * ================================
     */
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('stories')->group(function () {
            Route::post('/', [StoryController::class, 'store']); // Create a new story
            Route::post('/{story}/like', [StoryController::class, 'like']); // Like a story
            Route::delete('/{story}/like', [StoryController::class, 'unlike']); // Unlike a story
            Route::get('me', [StoryController::class, 'myStories']); // Get logged-in user's stories
        });

        // ================================
        // Story Highlights Routes
        // ================================
        Route::prefix('highlights')->group(function () {
            Route::get('/', [StoryHighlightController::class, 'index']); // Get all highlights
            Route::post('/', [StoryHighlightController::class, 'store']); // Create a new highlight
            Route::post('/{id}/add', [StoryHighlightController::class, 'addStory']); // Add story to highlight
            Route::post('/{id}/remove', [StoryHighlightController::class, 'removeStory']); // Remove story from highlight
            Route::get('/{id}', [StoryHighlightController::class, 'show']); // Get a specific highlight
        });
    });
});
