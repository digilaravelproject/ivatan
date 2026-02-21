<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\Story\StoryController;
use App\Http\Controllers\Api\Story\StoryHighlightController;
use App\Http\Controllers\Api\UserPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;


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






Route::prefix('v1')->group(function () {
    Route::get('posts', [UserPostController::class, 'index']);
    Route::get('posts/{post}', [UserPostController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('posts', [UserPostController::class, 'store']);
        Route::delete('posts/{post}', [UserPostController::class, 'destroy']);
        Route::post('/posts/{id}/like', [UserPostController::class, 'like']);
        Route::post('/posts/{id}/unlike', [UserPostController::class, 'unlike']);

        // ✅ View comment or reply method 1
        // Route::get('/comments/{commentableType}/{commentableId}', [CommentController::class, 'index']);
        // Method 2
        Route::get('/posts/{post}/comments', [CommentController::class, 'postComments']);


        // ✅ Post a new comment or reply
        Route::post('/comments', [CommentController::class, 'store']);

        // ✅ Delete a comment or reply
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

        // ✅ Toggle like/unlike on a comment or reply
        Route::post('/comments/{comment}/like', [CommentController::class, 'toggleCommentLike']);

        Route::get('/reels', [UserPostController::class, 'reels']);
    });
});




// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/comments', [CommentController::class, 'store']); // Add comment or reply
//     Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike']); // Like/Unlike
//     Route::delete('/comments/{comment}', [CommentController::class, 'destroy']); // Delete comment

//     Route::get('/comments/{type}/{id}', [CommentController::class, 'index']); // Get comments on Post/Reel etc.

//     Route::post('/user-posts/{id}/like', [UserPostController::class, 'like']);
//     Route::post('/user-posts/{id}/unlike', [UserPostController::class, 'unlike']);
// });




Route::prefix('v1')->group(function () {
    Route::get('stories', [StoryController::class, 'index']);
    Route::get('stories/{story}', [StoryController::class, 'show']);
    Route::get('stories/user/{userId}', [StoryController::class, 'index']); // optional: filter by user

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('stories', [StoryController::class, 'store']);
        Route::post('stories/{story}/like', [StoryController::class, 'like']);
        Route::delete('stories/{story}/like', [StoryController::class, 'unlike']);
        Route::get('me/stories', [StoryController::class, 'myStories']);

        // highlights
        Route::get('highlights', [StoryHighlightController::class, 'index']);
        Route::post('highlights', [StoryHighlightController::class, 'store']);
        Route::post('highlights/{id}/add', [StoryHighlightController::class, 'addStory']);
        Route::post('highlights/{id}/remove', [StoryHighlightController::class, 'removeStory']);
        Route::get('highlights/{id}', [StoryHighlightController::class, 'show']);
    });
});
