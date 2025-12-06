<?php

use App\Http\Controllers\Api\Ad\AdController;
use App\Http\Controllers\Api\Ad\AdPaymentController;
use App\Http\Controllers\Api\Ad\AdServingController;
use App\Http\Controllers\Api\Ecommerce\CartController;
use App\Http\Controllers\Api\Ecommerce\CheckoutController;
use App\Http\Controllers\Api\Ecommerce\PaymentController;
use App\Http\Controllers\Api\Ecommerce\ShippingController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\InterestController;
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
use App\Http\Controllers\CacheClearController;

/**
 * Public Routes (No authentication required)
 */
Route::get('interests', [InterestController::class, 'index']);
Route::post('auth/register', [UserController::class, 'register']);
Route::post('auth/login', [UserController::class, 'login']);
Route::post('check-username', [UserController::class, 'checkUsernameAvailability']);



/**
 * Versioned API Routes (v1)
 */
Route::prefix('v1')->group(function () {
    // clear Cache
    Route::get('/clear-cache', [CacheClearController::class, 'clearAllCache']);
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

            // 1. Feeds (Algorithm Based)
            Route::get('/', 'index');           // Mixed Feed
            Route::get('/feed/images', 'postsFeed');  // Only Images/Text
            Route::get('/feed/videos', 'videosFeed'); // Only Videos
            Route::get('/feed/reels', 'reelsFeed');   // Only Reels

            // 2. Post Operations
            Route::post('/', 'store');
            Route::get('/{post}', 'show');
            Route::delete('/{post}', 'destroy');

            // 3. Toggle Like (Single Endpoint)
            Route::post('/{id}/like', 'toggleLike');
            Route::get('/user/{username}', 'getPostsByUser');
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
        Route::prefix('comments')->middleware('auth:sanctum')->group(function () {
            // 1. Specific Actions (Must be defined BEFORE dynamic wildcards)
            Route::get('/post/{postId}', [CommentController::class, 'postComments']);
            Route::post('/like/{commentId}', [CommentController::class, 'toggleCommentLike']);
            Route::delete('/{commentId}', [CommentController::class, 'destroy']);

            // 2. Dynamic Route (Catches /{type}/{id}...) - Must be LAST
            Route::post('/{commentable_type}/{commentable_id}/{parent_id?}', [CommentController::class, 'store']);
        });



        /**
         * ================================
         * Authenticated Story Routes
         * ================================
         */
        Route::prefix('stories')->group(function () {

            // Feeds
            Route::get('/feed', [StoryController::class, 'index']);
            Route::get('/me', [StoryController::class, 'myStories']);
            Route::get('/user/{username}', [StoryController::class, 'getStoriesByUsername']);

            // Single Story CRUD
            Route::post('/', [StoryController::class, 'store']);
            Route::get('/{id}', [StoryController::class, 'show']);
            Route::delete('/{id}', [StoryController::class, 'destroy']);

            // Engagement
            Route::post('/{id}/view', [StoryController::class, 'markAsViewed']);
            Route::post('/{id}/like', [StoryController::class, 'toggleLike']);

            /* -------------------------- Highlights Sub-Routes ------------------------- */

            Route::prefix('highlights')->group(function () {

                // Get specific user's highlights (e.g. @john_doe)
                Route::get('/user/{username}', [StoryHighlightController::class, 'index']);

                // Get Single Highlight Details
                Route::get('/{id}', [StoryHighlightController::class, 'show']);

                // Manage Highlights
                Route::post('/', [StoryHighlightController::class, 'store']);
                Route::post('/{highlightId}/{storyId}', [StoryHighlightController::class, 'addStory']); // Add
                Route::delete('/{highlightId}/{storyId}', [StoryHighlightController::class, 'removeStory']); // Remove
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

        Route::middleware('auth:sanctum')->prefix('chats')->controller(ChatController::class)->group(function () {

            // Inbox
            Route::get('/', 'index'); // ?filter=groups

            // Create/Open
            Route::post('/private', 'openPrivate');
            Route::post('/group', 'createGroup'); // Needs CreateGroupChatRequest

            // Single Chat
            Route::prefix('{chat}')->group(function () {
                Route::get('/', 'show');
                Route::get('/messages', 'messages');
                Route::post('/messages', 'sendMessage');
                Route::post('/read', 'markRead');

                // Group Actions (Can separate controller later)
                Route::post('/participants', 'addParticipants');
                Route::post('/leave', 'leaveGroup');
            });

            // Message Actions
            Route::delete('/messages/{message}', 'deleteMessage'); // Payload: { delete_for_everyone: true/false }
        });

        // Route::prefix('chats')->controller(ChatController::class)->group(function () {

        //     // 1. Inbox & Single Chat
        //     Route::get('/', 'index');                  // List all chats
        //     Route::get('/{chat}', 'show')->whereNumber('chat'); // Chat details

        //     // 2. Start Private Chat
        //     Route::post('/private', 'openPrivate');    // Start/Open private chat

        //     // 3. Group Management
        //     Route::prefix('group')->group(function () {
        //         Route::post('/', 'createGroup');       // Create group
        //         Route::post('/{chat}/participants', 'addParticipants'); // Add members
        //         Route::delete('/{chat}/participants/{userId}', 'removeParticipant'); // Remove member
        //         Route::post('/{chat}/leave', 'leaveOrRemove'); // Leave or bulk remove
        //     });

        //     // 4. Messages & Interactions
        //     Route::post('/{chat}/messages', 'sendMessage');  // Send message
        //     Route::get('/{chat}/messages', 'messages');      // Get messages (Lazy load)
        //     Route::post('/read/{chat}', 'markRead');         // Mark messages as read
        // });




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
