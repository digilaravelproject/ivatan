<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\FollowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    // Follow a user
    Route::post('/follow/{userId}', [FollowController::class, 'follow']);

    // Unfollow a user
    Route::delete('/unfollow/{userId}', [FollowController::class, 'unfollow']);

    // Get followers of a user
    Route::get('/user/{userId}/followers', [FollowController::class, 'getFollowers']);

    // Get users being followed by a user
    Route::get('/user/{userId}/following', [FollowController::class, 'getFollowing']);

    Route::prefix('/posts')->controller(PostController::class)->group(function () {
        Route::post('/',  'create');
        Route::post('/{id}',  'showPostDetails');

        // Like a post
        Route::post('/{id}/like', 'like');

        // Unlike a post
        Route::delete('/{id}/like', 'unlike');

        // Add a comment to a post
        Route::post('/{id}/comment', 'comment');

        // Remove a comment from a post
        Route::delete('/{postId}/comment/{commentId}', 'deleteComment');
        // Like a Comment
        Route::post('/{id}/like/comment', 'likeComment');

        // Unlike a Comment
        Route::delete('/{id}/like/comment', 'unlikeComment');

        // Get the feed (list of posts)
        Route::get('/feed', 'feed');
    });
});
