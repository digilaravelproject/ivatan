<?php

use App\Http\Controllers\Api\Ad\AdController;
use App\Http\Controllers\Api\Ad\AdPaymentController;
use App\Http\Controllers\Api\Ad\AdServingController;
use App\Http\Controllers\Api\Ecommerce\CartController;
use App\Http\Controllers\Api\Ecommerce\CheckoutController;
use App\Http\Controllers\Api\Ecommerce\AddressController;
use App\Http\Controllers\Api\Ecommerce\PaymentController;
use App\Http\Controllers\Api\Ecommerce\ShippingController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\Api\Jobs\JobApplicationController;
use App\Http\Controllers\Api\Jobs\JobPostController;
use App\Http\Controllers\Api\Jobs\RecruiterJobController;
use App\Http\Controllers\Api\Seller\UserProductController;
use App\Http\Controllers\Api\Seller\UserServiceController;
use App\Http\Controllers\Api\Story\StoryController;
use App\Http\Controllers\Api\Story\StoryHighlightController;
use App\Http\Controllers\Api\UserPostController;
use App\Http\Controllers\Api\Chat\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\Contact\ContactSyncController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Ecommerce\OrderController;
use App\Http\Controllers\Api\Ecommerce\EnquiryController;
use App\Http\Controllers\Api\Ecommerce\MarketplaceController;
use App\Http\Controllers\Api\Seller\UserSellerController;
use App\Http\Controllers\Api\Seller\SellerFinancialController;
use App\Http\Controllers\Api\Seller\SellerTransactionController;
use App\Http\Controllers\Api\Seller\SellerOrderController;
use App\Http\Controllers\Api\ViewController;
use App\Http\Controllers\Api\UserInteractionController;
use App\Http\Controllers\CacheClearController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\MobileLoginController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\BannerController;



// use Illuminate\Support\Facades\Artisan;

// Route::get('/run-migration', function (Illuminate\Http\Request $request) {

//     // 🔒 SECURITY: Bina key ke run mat karne dena
//     // Browser me ?key=my_secret_pass lagana padega
//     if ($request->query('key') !== 'my_secret_pass_123') {
//         return response()->json(['error' => 'Unauthorized access!'], 403);
//     }

//     try {
//         // --force lagana zaruri hai production server ke liye
//         Artisan::call('migrate', ["--force" => true]);

//         return response()->json([
//             'status' => true,
//             'message' => 'Migrations executed successfully!',
//             'output' => Artisan::output(), // Console ka output dikhega
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => false,
//             'error' => $e->getMessage()
//         ], 500);
//     }
// });
// use Illuminate\Support\Facades\DB;

// Route::get('/update-country-code', function () {

//     // Direct SQL Update - Yeh bohot fast hai
//     // Saare users ka country_code '+91' set kar dega
//     DB::table('users')->update(['country_code' => '+91']);

//     return response()->json([
//         'message' => 'Success! Sabhi users ka country_code +91 set ho gaya hai.'
//     ]);
// });
/**
 * Public Routes (No authentication required)
 */
Route::get('interests', [InterestController::class, 'index']);
Route::post('auth/register', [UserController::class, 'register']);
Route::post('auth/login', [UserController::class, 'login']);
Route::post('auth/google-login', [GoogleAuthController::class, 'login']);
Route::post('auth/mobile_login', [MobileLoginController::class, 'loginWithMobile']);
Route::post('check-username', [UserController::class, 'checkUsernameAvailability']);

Route::post('forgot-password/verify', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('forgot-password/reset', [ForgotPasswordController::class, 'resetPassword']);






/**
 * Versioned API Routes (v1)
 */
Route::prefix('v1')->group(function () {
    // clear Cache
    Route::get('/clear-cache', [CacheClearController::class, 'clearAllCache']);
    Route::get('/banners', [BannerController::class, 'index']);


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
            Route::get('/video/{id}/related', 'getRelatedVideos'); // Related Videos
            Route::get('/feed/reels', 'reelsFeed');   // Only Reels

            // 2. Post Operations
            Route::post('/', 'store');
            Route::get('/{post}', 'show');
            Route::delete('/{post}', 'destroy');

            // 3. Toggle Like (Single Endpoint)
            Route::post('/{id}/like', 'toggleLike');

            Route::post('/{id}/report', 'reportPost');

            Route::get('/user/{username}', 'getPostsByUser');
            Route::get('/feed/trending', 'globalTrendingFeed'); // Global Trending
            Route::get('/feed/trending/interests', 'trendingInterestsFeed'); // User Interest Based Trending
            Route::get('/feed/for-you', 'forYouFeed'); // Shuffled Interest Based Feed
        });

        // ================================
        // User Interactions (Bookmark, Block, Preferences)
        // ================================
        Route::post('/posts/{id}/bookmark', [UserInteractionController::class, 'toggleBookmark']);
        Route::get('/user/bookmarks', [UserInteractionController::class, 'getBookmarks']);
        Route::post('/users/{id}/block', [UserInteractionController::class, 'toggleBlock']);
        Route::get('/user/blocked-users', [UserInteractionController::class, 'getBlockedUsers']);
        Route::post('/posts/{id}/interested', [UserInteractionController::class, 'markInterested']);
        Route::post('/posts/{id}/not-interested', [UserInteractionController::class, 'markNotInterested']);
        Route::delete('/posts/{id}/preference', [UserInteractionController::class, 'removePreference']);

        Route::post('/contacts/sync', [ContactSyncController::class, 'sync']);
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

        // User Enquiries (Buyer side)
        Route::get('user/my-enquiries', [EnquiryController::class, 'myEnquiries']);

        // ================================
        // Marketplace Routes (Public)
        // ================================
        Route::prefix('marketplace')->group(function () {
            Route::get('products', [MarketplaceController::class, 'getProducts']);
            Route::get('services', [MarketplaceController::class, 'getServices']);
            Route::get('products/{productIdentifier}', [UserProductController::class, 'show']);
            Route::get('services/{serviceIdentifier}', [UserServiceController::class, 'show']);
        });
        // Public Enquiry (Rate limited: 5 requests per minute)
        Route::post('enquiries', [EnquiryController::class, 'store'])->middleware('throttle:5,1');
        // ================================
        // Seller Routes
        // ================================
        Route::prefix('seller')->group(function () {
            Route::post('/', [UserSellerController::class, 'toggleSelf']);
            Route::get('dashboard/stats', [UserSellerController::class, 'getDashboardStats']);
            Route::get('products', [UserProductController::class, 'index']);
            Route::get('{sellerId}/products', [UserProductController::class, 'getSellerProducts']);
            Route::post('products', [UserProductController::class, 'store']);
            Route::get('products/{productIdentifier}', [UserProductController::class, 'show']);
            Route::match(['put', 'patch', 'post'], 'products/{product}', [UserProductController::class, 'update']);
            Route::delete('products/{product}', [UserProductController::class, 'destroy']);

            // Seller Enquiry Dashboard
            Route::get('enquiries', [EnquiryController::class, 'index']);
            Route::get('enquiries/stats', [EnquiryController::class, 'stats']);
            Route::post('enquiries/{identifier}/status', [EnquiryController::class, 'updateStatus']);
            Route::delete('enquiries/{identifier}', [EnquiryController::class, 'destroy']);

            // Seller Financial Details
            Route::get('financials', [SellerFinancialController::class, 'show']);
            Route::post('financials', [SellerFinancialController::class, 'store']);
            Route::delete('financials', [SellerFinancialController::class, 'destroy']);

            Route::get('transactions', [SellerTransactionController::class, 'index']);

            // Seller Order Management
            Route::get('orders', [SellerOrderController::class, 'index']);
            Route::get('orders/{id}', [SellerOrderController::class, 'show']);
            Route::post('orders/{id}/status', [SellerOrderController::class, 'updateStatus']);
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

        // ================================
        // Address Routes
        // ================================
        Route::prefix('addresses')->group(function () {
            Route::get('/', [AddressController::class, 'index']);
            Route::post('/', [AddressController::class, 'store']);
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

        // /*
        // |--------------------------------------------------------------------------
        // | Shipping
        // |--------------------------------------------------------------------------
        // */
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

            Route::get('my/applications', [JobApplicationController::class, 'myApplications']); // List logged-in user's applications
            Route::get('my/profile', [JobApplicationController::class, 'profile']); // Get logged-in user's career profile

            // Recruiter specific management routes
            Route::prefix('recruiter')->controller(RecruiterJobController::class)->group(function () {
                Route::get('/', 'index');                  // List recruiter's jobs
                Route::put('{job}/status', 'updateStatus'); // Update job status
            });

            // JobApplicationController routes
            Route::controller(JobApplicationController::class)->group(function () {
                Route::post('{jobId}/apply', 'apply');               // Apply to a job
                Route::get('{job}/applications', 'listByJob');    // List applications for a job
                Route::post('applications/{application}/status', 'updateStatus'); // Update application status
                Route::get('applications/{application}/resume', 'downloadResume'); // Download resume
            });

            // JobPostController routes
            Route::controller(JobPostController::class)->group(function () {
                Route::get('/', 'index');           // List all jobs
                Route::post('/', 'store');          // Create new job
                Route::put('{job}', 'update');      // Update job
                Route::delete('{job}', 'destroy');  // Delete job
                Route::get('{identifier}', 'show');        // Show single job (Wildcard, must be last)
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
