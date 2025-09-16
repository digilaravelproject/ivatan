<?php

use App\Http\Controllers\Api\Ecommerce\CartController;
use App\Http\Controllers\Api\Ecommerce\CheckoutController;
use App\Http\Controllers\Api\Ecommerce\PaymentController;
use App\Http\Controllers\Api\Ecommerce\ShippingController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\Jobs\JobApplicationController;
use App\Http\Controllers\Api\Jobs\JobPostController;
use App\Http\Controllers\Api\Seller\UserProductController;
use App\Http\Controllers\Api\Seller\UserServiceController;
use App\Http\Controllers\Api\Story\StoryController;
use App\Http\Controllers\Api\Story\StoryHighlightController;
use App\Http\Controllers\Api\UserPostController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Chat\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\Ecommerce\OrderController;
use App\Http\Controllers\Api\Seller\UserSellerController;

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

        // ================================================================================================================================
        // Ecommerce Routes
        // ================================================================================================================================

        // ================================
        // Seller Routes
        // ================================
        Route::prefix('seller')->group(function () {
            Route::post('/', [UserSellerController::class, 'toggleSelf']);
            Route::get('products', [UserProductController::class, 'index']);
            Route::post('products', [UserProductController::class, 'store']);
            Route::get('products/{product}', [UserProductController::class, 'show']);
            Route::put('products/{product}', [UserProductController::class, 'update']);
            Route::delete('products/{product}', [UserProductController::class, 'destroy']);
        });

        // ================================
        // Cart Routes
        // ================================
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);            // View cart
            // Route::post('/add', [CartController::class, 'add']);          // Add item
            Route::post('/', [CartController::class, 'add']);          // Add item
            Route::post('/update/{id}', [CartController::class, 'update']); // Update qty
            Route::delete('/remove/{id}', [CartController::class, 'remove']); // Remove item
            Route::delete('/clear', [CartController::class, 'clear']);    // Clear cart

        });

        /*
        |--------------------------------------------------------------------------
        | Checkout
        |--------------------------------------------------------------------------
        */
        Route::post('/checkout', [CheckoutController::class, 'checkout']);

        /*
        |--------------------------------------------------------------------------
        | Razorpay Payment
        |--------------------------------------------------------------------------
        */
        Route::post('/payment/razorpay/order', [PaymentController::class, 'createRazorpayOrder']);
        Route::post('/payment/razorpay/verify', [PaymentController::class, 'verifyRazorpayPayment']);

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */
        Route::get('/orders', [OrderController::class, 'index']);                // List user's orders
        Route::get('/orders/{order}', [OrderController::class, 'show']);         // Show specific order
        Route::delete('/orders/{order}', [OrderController::class, 'destroy']);   // Delete order (if allowed)

        /*
        |--------------------------------------------------------------------------
        | Shipping
        |--------------------------------------------------------------------------
        */
        Route::get('/orders/{orderId}/shipping', [ShippingController::class, 'getShipping']);       // Get order shipping status
        Route::post('/orders/{orderId}/shipping', [ShippingController::class, 'updateShipping']);    // Update shipping info (admin/partner)

        // ================================
        // Services Routes
        // ================================
        Route::prefix('services')->group(function () {
            // Seller services
            Route::get('/', [UserServiceController::class, 'index']);
            Route::post('/', [UserServiceController::class, 'store']);
            Route::get('/{service}', [UserServiceController::class, 'show']);
            Route::post('/{service}', [UserServiceController::class, 'update']);
            Route::delete('/{service}', [UserServiceController::class, 'destroy']);
        });

        // ================================
        // Jobs Routes
        // ================================
        // JobPostController routes
        Route::prefix('jobs')->controller(JobPostController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('{job}', 'show');
            Route::post('/', 'store');
            Route::put('{job}', 'update');
            Route::delete('{job}', 'destroy');
        });

        // JobApplicationController routes
        Route::controller(JobApplicationController::class)->group(function () {
            Route::post('jobs/apply', 'apply');
            Route::get('jobs/{job}/applications', 'listByJob');
            Route::post('applications/{application}/status', 'updateStatus');
            Route::get('applications/{application}/resume', 'downloadResume');
            // List logged-in user's applications (job seeker)
            Route::get('/my/applications',  'myApplications');
        });





        // ================================
        // Chats Routes
        // ================================

        // 1. All chats (list of conversations user is part of)
        Route::get('chats', [ChatController::class, 'index']);

        // 2. Create chats
        Route::post('chats/private', [ChatController::class, 'openPrivate']); // create/open private chat
        Route::post('chats/group', [ChatController::class, 'createGroup']);   // create group chat

        // 3. Group participants management
        Route::post('chats/{chat}/participants', [ChatController::class, 'addParticipants']);     // add members
        Route::delete('chats/{chat}/participants/{userId}', [ChatController::class, 'removeParticipant']); // remove member
        Route::post('chats/{chat}/leave', [ChatController::class, 'leave']); // leave group

        // 4. Messaging
        Route::post('chats/messages', [ChatController::class, 'sendMessage']); // send new message
        Route::get('chats/{chat}/messages', [ChatController::class, 'messages']); // get all messages in chat

        // 5. Read / Seen
        Route::post('chats/read', [ChatController::class, 'markRead']); // mark messages as read

        // 6. Single chat details (optional)
        Route::get('chats/{chat}', [ChatController::class, 'show'] ?? function () {
            return response()->json(['message' => 'Implement show if needed'], 200);
        });
    });
});
