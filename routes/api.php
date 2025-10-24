<?php

use App\Http\Controllers\Api\Ad\AdController;
use App\Http\Controllers\Api\Ad\AdPaymentController;
use App\Http\Controllers\Api\Ad\AdServingController;
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
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Ecommerce\OrderController;
use App\Http\Controllers\Api\Seller\UserSellerController;
use App\Http\Controllers\Api\ViewController;

/**
 * Public Routes (No authentication required)
 */
Route::post('auth/register', [UserController::class, 'register']);
Route::post('auth/login', [UserController::class, 'login']);
Route::post('check-username', [UserController::class, 'checkUsernameAvailability']);



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
        // User Logout & Update
        // ================================

        Route::delete('/auth/logout', [UserController::class, 'logout']);
        Route::post('/auth/update', [UserController::class, 'update']);
        // Fetch User by Username
        Route::get('users/{username}', [UserController::class, 'show']);

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
            Route::post('/reels',  'reels');
        });
        // ================================
        // View Management Routes
        // ================================
        Route::post('/posts/{post}/view', [ViewController::class, 'trackPost']);
        Route::post('/view/{type}/{id}', [ViewController::class, 'track']);
        // 'post'   => UserPost::class,
        // 'job'    => UserJobPost::class,
        // 'story'  => UserStory::class,

        // ================================
        // Comment Routes
        // ================================
        Route::prefix('comments')->group(function () {
            // ✅ View comment or reply method 1
            // Route::get('/{commentableType}/{commentableId}', [CommentController::class, 'index']);
            // Method 2
            Route::get('/{post}', [CommentController::class, 'postComments']);

            // Toggle like/unlike on a comment
            // Delete comment
            Route::delete('/{comment}', [CommentController::class, 'destroy']);
            Route::post('like/{comment}', [CommentController::class, 'toggleCommentLike']);
            // Post a new comment or reply

            // Route::post('/', [CommentController::class, 'store_old']);
            Route::post('{commentable_type}/{commentable_id}/{parent_id?}', [CommentController::class, 'store']);
        });



        /**
         * ================================
         * Authenticated Story Routes
         * ================================
         */
        Route::prefix('stories')->group(function () {
            // Public Story Routes
            Route::get('/', [StoryController::class, 'index']); // Get all stories
            Route::get('me', [StoryController::class, 'myStories']); // Get logged-in user's stories
            Route::get('/{story}', [StoryController::class, 'show'])->where('story', '[0-9]+'); // Get single story by ID
            Route::get('/user/{username}', [StoryController::class, 'getUserStories']); // Get stories by user
            Route::post('/', [StoryController::class, 'store']); // Create a new story
            Route::post('/{story}/like', [StoryController::class, 'like']); // Like a story
            Route::delete('/{story}/like', [StoryController::class, 'unlike']); // Unlike a story
            // Story Highlights Routes (Under stories prefix)
            Route::prefix('highlights')->group(function () {
                // Get all highlights Logged-in users
                Route::get('/', [StoryHighlightController::class, 'index']);

                // Get highlights of a specific user
                // If no user ID is provided, it may default to current user or all users depending on implementation
                Route::get('/user/{user?}', [StoryHighlightController::class, 'getUserHighlights']);

                // Create a new highlight for the authenticated user
                Route::post('/', [StoryHighlightController::class, 'store']);

                // Add a specific story to a highlight
                // URL format: /highlights/{highlightId}/{storyId}/add
                Route::post('/{highlightId}/{storyId}/add', [StoryHighlightController::class, 'addStory']);

                // Remove a specific story from a highlight
                // URL format: /highlights/{highlightId}/{storyId}/remove
                Route::post('/{highlightId}/{storyId}/remove', [StoryHighlightController::class, 'removeStory']);

                // Get detailed information for a specific highlight, including all its stories
                Route::get('/{highlightId}', [StoryHighlightController::class, 'show']);
            });
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
            Route::get('{sellerId}/products', [UserProductController::class, 'getSellerProducts']);
            Route::post('products', [UserProductController::class, 'store']);
            Route::get('products/{productIdentifier}', [UserProductController::class, 'show']);
            Route::match(['put', 'patch', 'post'], 'products/{product}', [UserProductController::class, 'update']);
            Route::delete('products/{product}', [UserProductController::class, 'destroy']);
        });
        // ================================
        // Services Routes
        // ================================
        Route::prefix('services')->group(function () {
            // Seller services
            Route::get('/', [UserServiceController::class, 'index']);
            Route::get('{sellerId}/services', [UserServiceController::class, 'getSellerServices']);
            Route::post('/', [UserServiceController::class, 'store']);
            Route::get('/{serviceIdentifier}', [UserServiceController::class, 'show']);
            Route::post('/{service}', [UserServiceController::class, 'update']);
            Route::delete('/{service}', [UserServiceController::class, 'destroy']);
        });
        // ================================
        // Cart Routes
        // ================================
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);            // View cart
            // Route::post('/add', [CartController::class, 'add']);          // Add item
            Route::post('/', [CartController::class, 'add']);          // Add item
            Route::post('{id}', [CartController::class, 'update']); // Update qty
            Route::delete('{id}', [CartController::class, 'remove']); // Remove item
            Route::delete('/', [CartController::class, 'clear']);    // Clear cart

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
        // Route::get('/orders', [OrderController::class, 'index']);                // List user's orders
        // Route::get('/orders/{order}', [OrderController::class, 'show']);         // Show specific order
        // Route::delete('/orders/{order}', [OrderController::class, 'destroy']);   // Delete order (if allowed)

        // /*
        // |--------------------------------------------------------------------------
        // | Shipping
        // |--------------------------------------------------------------------------
        // */
        // Route::get('/orders/{orderId}/shipping', [ShippingController::class, 'getShipping']);       // Get order shipping status
        // Route::post('/orders/{orderId}/shipping', [ShippingController::class, 'updateShipping']);    // Update shipping info (admin/partner)
        Route::prefix('orders')->group(function () {

            // Orders routes
            Route::get('/', [OrderController::class, 'index']);                // List user's orders
            Route::get('/{order}', [OrderController::class, 'show']);         // Show specific order
            Route::delete('/{order}', [OrderController::class, 'destroy']);   // Delete order (if allowed)

            // Shipping routes
            Route::get('/{orderId}/shipping', [ShippingController::class, 'getShipping']);       // Get order shipping status
            Route::post('/{orderId}/shipping', [ShippingController::class, 'updateShipping']);    // Update shipping info (admin/partner)

        });


        // ================================
        // Jobs Routes
        // ================================
        Route::prefix('jobs')->group(function () {

            // JobPostController routes
            Route::controller(JobPostController::class)->group(function () {
                Route::get('/', 'index');           // List all jobs
                Route::get('{identifier}', 'show');        // Show single job
                Route::post('/', 'store');          // Create new job
                Route::put('{job}', 'update');      // Update job
                Route::delete('{job}', 'destroy');  // Delete job
            });

            Route::get('my/applications', [JobApplicationController::class, 'myApplications']); // List logged-in user's applications

            // JobApplicationController routes
            Route::controller(JobApplicationController::class)->group(function () {
                Route::post('{jobId}/apply', 'apply');               // Apply to a job
                Route::get('{job}/applications', 'listByJob');    // List applications for a job
                Route::post('applications/{application}/status', 'updateStatus'); // Update application status
                Route::get('applications/{application}/resume', 'downloadResume'); // Download resume

            });
        });




        // ================================
        // Chats Routes
        // ================================

        Route::prefix('chats')->group(function () {

            /**
             * 1. Chats - List of Conversations User is Part Of
             */
            Route::get('/', [ChatController::class, 'index']);  // All chats

            /**
             * 2. Create Chats
             */
            Route::post('private', [ChatController::class, 'openPrivate']); // Create/Open private chat

            // ================================
            // Group Chat Routes
            // ================================
            Route::prefix('group')->group(function () {
                /**
                 * Group Participants Management
                 */
                Route::post('/', [ChatController::class, 'createGroup']);   // Create group chat
                Route::post('{chat}/participants', [ChatController::class, 'addParticipants']);  // Add members to a group chat
                Route::delete('{chat}/participants/{userId}', [ChatController::class, 'removeParticipant']); // Remove a member from group chat
                Route::post('{chat}/leave', [ChatController::class, 'leaveOrRemove']); // User leaves the group chat
            });

            /**
             * 3. Messaging
             */
            Route::post('{chat}/messages', [ChatController::class, 'sendMessage']); // Send a new message to a chat
            Route::get('{chat}/messages', [ChatController::class, 'messages']); // Get all messages in a chat

            /**
             * 4. Read / Seen
             */
            Route::post('read/{chat}', [ChatController::class, 'markRead']); // Mark messages as read

            /**
             * 5. Single Chat Details (Optional)
             */
            Route::get('{chat}', [ChatController::class, 'show']);
        });




        // ================================
        // Notification Routes
        // ================================
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('notifications/mark-read', [NotificationController::class, 'markRead']);
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

        // optional test endpoint
        Route::post('notifications/send-test', [NotificationController::class, 'sendTest']);


        // ================================
        // Ad Routes
        // ================================
        Route::prefix('ads')->group(function () {

            // Ad routes
            Route::controller(AdController::class)->group(function () {
                Route::get('ad-packages', 'adPackages');      // GET /ads/ad-packages
                Route::get('my', 'myAds');                    // GET /ads/my
                Route::post('/', 'store');                    // POST /ads
                Route::get('{ad}', 'show')->whereNumber('ad'); // GET /ads/{ad}
            });

            // AdPayment routes under /ads prefix
            Route::controller(AdPaymentController::class)->group(function () {
                Route::get('{ad}/pending-order', 'getPendingOrder');   // GET /ads/{ad}/pending-order
                Route::post('payments/verify', 'verify');               // POST /ads/payments/verify
            });

            // ================================
            // Fetch Ads
            // ================================
            Route::get('serve', [AdServingController::class, 'serveAd']); // GET /api/ads/serve
        });
    });
});
