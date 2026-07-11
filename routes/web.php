<?php

use App\Http\Controllers\Admin\Ad\AdminAdController;
use App\Http\Controllers\Admin\Ad\AdPackageController;
use App\Http\Controllers\Admin\Ecommerce\ProductController;
use App\Http\Controllers\Admin\Ecommerce\ServiceController;
use App\Http\Controllers\Admin\Ecommerce\AdminJobController;
use App\Http\Controllers\Admin\Ecommerce\AdminApplicationController;
use App\Http\Controllers\Admin\InterestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminReportedPostController;
use App\Http\Controllers\Admin\AdminBannerController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\Ecommerce\OrderController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\FollowController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\LiveChatGroupController;
use App\Http\Controllers\Admin\ServerHealthController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminSubscriptionPlanController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminProfileApprovalController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminWalletController;
use App\Http\Controllers\Admin\ExclusiveContentController as AdminExclusiveContentController;

// Public Routes
Route::get('/', fn() => view('web.index'))->name('web.index');
Route::get('/market', fn() => view('web.market'))->name('web.market');
Route::get('/privacy-policy', fn() => view('web.privacy'))->name('web.privacy');
Route::get('/quickhire', fn() => view('web.quickhire'))->name('web.quickhire');
Route::get('/terms-n-conditions', fn() => view('web.terms'))->name('web.terms');
Route::get('/trust', fn() => view('web.trust'))->name('web.trust');
Route::get('/contcat-us', fn() => view('web.contact'))->name('web.contact');
Route::get('/child-safety', fn() => view('web.child_safety'))->name('web.child.safety');
Route::get('/payment-and-refund-policy', fn() => view('web.financial-policy'))->name('web.financial-policy');
Route::get('/pricing-plan', fn() => view('web.pricing'))->name('web.pricing-plan');

// =====================
// Payment Callback (PhonePe redirects users here)
// =====================
Route::match(['get', 'post'], 'payment/callback/{gateway}', [\App\Http\Controllers\Api\PaymentCallbackController::class, 'handle'])
    ->name('payment.callback')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Route::get('/', fn() => redirect('/admin'));
Route::get('/admin', fn() => view('auth.login'));

// Dashboard (User)
// Route::middleware(['auth', 'verified'])->get('/dashboard', fn() => view('dashboard'))->name('dashboard');
Route::middleware(['auth', 'verified'])->get('/dashboard', fn() => redirect('/admin/dashboard'))->name('dashboard');

// Authenticated User Profile
Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

// =====================
// Admin Routes
// =====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'is_admin'])->group(function () {
    // Route::get('/dashboard',
    // Dashboard Controller
    Route::controller(DashboardController::class)->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/summary', 'summary')->name('summary');
        Route::get('/chart/{type}/{days?}', 'chart')->name('chart');
        Route::get('/activity', 'activityFeed')->name('activityfeed');
    });
    // Server Health
    Route::controller(ServerHealthController::class)->prefix('server-health')->name('server-health.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/check/reverb', 'checkReverb')->name('check.reverb');
        Route::get('/check/queue', 'checkQueue')->name('check.queue');
        Route::get('/check/system', 'checkSystem')->name('check.system');
    });

    // ACreate interest routes
    // Interest CRUD
    Route::resource('interests', InterestController::class)->only(['index', 'store', 'destroy']);

    // Category Create
    Route::post('interests/category', [InterestController::class, 'storeCategory'])
        ->name('interests.category.store');

    // Category Update
    Route::put('interests/category/{category}', [InterestController::class, 'updateCategory'])
        ->name('interests.category.update');

    // Category Delete
    Route::delete('interests/category/{category}', [InterestController::class, 'destroyCategory'])
        ->name('interests.category.destroy');
    // Admin Profile
    Route::controller(AdminProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
    });

    // User Management
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/trashed', 'trashed')->name('trashed');
        Route::get('/{user}', 'show')->name('show');

        // Actions
        Route::put('/{user}/block', 'block')->name('block');
        Route::put('/{user}/unblock', 'unblock')->name('unblock');
        Route::put('/{user}/verify', 'verify')->name('verify');
        Route::put('/{user}/unverify', 'unverify')->name('unverify');
        Route::put('/{user}/seller', 'toggleSellerStatus')->name('seller.toggle');
        Route::put('/{user}/employer', 'toggleEmployerStatus')->name('employer.toggle');

        Route::delete('/{user}', 'destroy')->name('destroy');
        Route::post('/{user}/restore', 'restore')->name('restore');
        Route::post('/force-delete/{user}', 'forceDelete')->name('delete');
    });

    // User History
    Route::controller(HistoryController::class)->prefix('users/{user}/history')->name('users.history.')->group(function () {
        Route::get('likes',        'likes')->name('likes');
        Route::get('comments',     'comments')->name('comments');
        Route::get('video-views',  'videoViews')->name('video-views');
        Route::get('purchases',    'purchases')->name('purchases');
        Route::get('services',     'services')->name('services');
    });

    // Followers / Following
    Route::controller(FollowController::class)->prefix('user')->name('user.')->group(function () {
        Route::get('{userId}/followers', 'getFollowers')->name('follower');
        Route::get('{userId}/following', 'getFollowing')->name('following');
    });



    // User Posts List

    // Posts Management (Admin)
    Route::prefix('user-posts')->controller(AdminPostController::class)->name('userpost.')->group(function () {
        Route::put('/{postId}', 'update')->name('update');
        Route::get('/{postId}', 'show')->name('details');
        Route::get('/', 'index')->name('index');
        Route::get('/{postId}/likes', 'getLikes')->name('likes');
        Route::get('/{PostId}/comments', 'getComments')->name('comments');
        // Route::delete('/{postId}', 'destroy')->name('destroy');
        Route::delete('/{commentId}', 'deleteComment')->name('comments.delete');
        Route::post('/{postId}/soft-delete', 'softDelete')->name('softDelete');
    });

    // prefix admin.
    Route::get('reported-posts', [AdminReportedPostController::class, 'index'])
        ->name('reported-post.index');

    Route::get('reported-posts/{id}', [AdminReportedPostController::class, 'show'])
        ->name('reported-post.details');

    Route::post('reported-posts/{id}/status', [AdminReportedPostController::class, 'updateStatus'])
        ->name('reported-post.status');

    Route::delete('reported-posts/{id}', [AdminReportedPostController::class, 'softDelete'])
        ->name('reported-post.delete');

    Route::delete('reported-posts/{id}/force', [AdminReportedPostController::class, 'forceDelete'])
        ->name('reported-post.force-delete');

    Route::resource('banners', AdminBannerController::class)
        ->except(['show']);

    // route::delete('comments/{commentId}', [CommentController::class, 'destroy'])->name('comments.delete');
    // =====================
    // Product Management (Admin)
    // =====================


    Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{product}', 'show')->name('show');
        Route::post('/{product}/approve', 'approve')->name('approve');
        Route::post('/{product}/reject', 'reject')->name('reject');
        Route::post('/bulk/approve', 'bulkApprove')->name('bulk.approve');
        Route::post('/bulk/reject', 'bulkReject')->name('bulk.reject');
    });

    // =====================
    // Services Management (Admin)
    // =====================

    Route::prefix('services')->controller(ServiceController::class)->name('services.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{service}', 'show')->name('show');
        Route::post('/{service}/approve', 'approve')->name('approve');
        Route::post('/{services}/reject', 'reject')->name('reject');
        Route::post('/bulk/approve', 'bulkApprove')->name('bulk.approve');
        Route::post('/bulk/reject', 'bulkReject')->name('bulk.reject');
    });

    // =====================
    // Job Management Routes
    // =====================
    Route::prefix('jobs')->controller(AdminJobController::class)->name('jobs.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{job}', 'show')->name('show');
        Route::get('{job}/edit', 'edit')->name('edit');
        Route::put('{job}', 'update')->name('update');
        Route::delete('{job}', 'destroy')->name('destroy');

        // Applications under a job
        Route::get('{job}/applications', [AdminApplicationController::class, 'listByJob'])->name('applications');
    });

    // Application specific routes
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('{application}/resume', [AdminApplicationController::class, 'downloadResume'])->name('resume');
    });


    // =====================
    // Order Management (Admin)
    // =====================


    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // =====================
    // Ad Management (Admin)
    // =====================
    // Group admin ad routes
    Route::prefix('ads')->name('ads.')->controller(AdminAdController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{}/ads', 'index')->name('show');
        Route::get('pending', 'pending')->name('pending');
        Route::post('{ad}/approve', 'approve')->name('approve');
        Route::post('{ad}/reject', 'reject')->name('reject');
    });

    Route::prefix('ad-packages')->name('ad.')->controller(AdPackageController::class)->group(function () {
        route::get('/', 'index')->name('ad-packages.index');
        route::get('/create', 'create')->name('ad-packages.create');
        route::post('/', 'store')->name('ad-packages.store');
        route::get('/{adPackage}', 'show')->name('ad-packages.show');
        route::get('/{adPackage}/edit', 'edit')->name('ad-packages.edit');
        route::put('/{adPackage}', 'update')->name('ad-packages.update');
        route::delete('/{adPackage}', 'destroy')->name('ad-packages.destroy');
    });

    // =====================
    // Notifications (Admin)
    // =====================
    Route::prefix('notifications')->name('notifications.')->controller(AdminNotificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/unread-count', 'unreadCount')->name('unread-count');
        Route::get('/recent', 'recent')->name('recent');
        Route::post('/send', 'sendToUser')->name('send');
        Route::post('/broadcast', 'sendBroadcast')->name('broadcast');
        Route::get('/{id}', 'show')->name('show');
    });

    // =====================
    // Live Chat Groups (Admin)
    // =====================
    Route::prefix('live-chat-groups')->name('live-chat-groups.')->controller(LiveChatGroupController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{liveChatGroup}', 'show')->name('show');
        Route::get('/{liveChatGroup}/edit', 'edit')->name('edit');
        Route::put('/{liveChatGroup}', 'update')->name('update');
        Route::delete('/{liveChatGroup}', 'destroy')->name('destroy');
        Route::post('/{liveChatGroup}/remove-participant', 'removeParticipant')->name('remove-participant');
        Route::post('/{liveChatGroup}/ban-participant', 'banParticipant')->name('ban-participant');
        Route::post('/{liveChatGroup}/unban-participant', 'unbanParticipant')->name('unban-participant');
        Route::post('/{liveChatGroup}/mute-participant', 'muteParticipant')->name('mute-participant');
        Route::post('/{liveChatGroup}/sync-users', 'syncUsers')->name('sync-users');
        Route::get('/{liveChatGroup}/chat', 'chat')->name('chat');
        Route::get('/{liveChatGroup}/chat/messages', 'fetchMessages')->name('chat.messages');
        Route::post('/{liveChatGroup}/chat/messages', 'sendMessage')->name('chat.send');
    });

    // =====================
    // Subscription Management (Admin)
    // =====================
    Route::prefix('subscriptions')->name('subscriptions.')->controller(AdminSubscriptionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/user/{user}', 'userSubscriptions')->name('user');
        Route::get('/{subscription}', 'show')->name('show');
        Route::post('/{subscription}/cancel', 'cancel')->name('cancel');
        Route::post('/assign', 'assign')->name('assign');
    });

    // =====================
    // Subscription Plans (Admin)
    // =====================
    Route::resource('subscription-plans', AdminSubscriptionPlanController::class);

    // =====================
    // Invoices (Admin)
    // =====================
    Route::prefix('invoices')->name('invoices.')->controller(AdminInvoiceController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{invoice}', 'show')->name('show');
        Route::post('/{invoice}/resend', 'resend')->name('resend');
    });

    // =====================
    // Profile Approvals (Admin)
    // =====================
    Route::prefix('profile-approval')->name('profile-approval.')->controller(AdminProfileApprovalController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{request}', 'show')->name('show');
        Route::post('/{request}/approve', 'approve')->name('approve');
        Route::post('/{request}/reject', 'reject')->name('reject');
    });

    // =====================
    // Settings (Admin)
    // =====================
    Route::prefix('settings')->name('settings.')->controller(AdminSettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/update-payment', 'updatePayment')->name('update-payment');
        Route::post('/test-connection', 'testConnection')->name('test-connection');
        Route::post('/update-subscription', 'updateSubscription')->name('update-subscription');
        Route::post('/update-general', 'updateGeneral')->name('update-general');
    });

    // =====================
    // Exclusive Content & Wallet (Admin)
    // =====================
    Route::prefix('exclusive')->name('exclusive.')->group(function () {
        
        // Blade Views Routes
        Route::view('/moderation', 'admin.exclusive-content.moderation')->name('moderation');
        Route::view('/financial', 'admin.exclusive-content.financial')->name('financial');
        Route::view('/settings', 'admin.exclusive-content.settings')->name('settings');

        // Content Moderation API hooks
        Route::get('/enablements', [AdminExclusiveContentController::class, 'listEnablementRequests'])->name('enablements.list');
        Route::post('/enablements/{id}/approve', [AdminExclusiveContentController::class, 'approveEnablement'])->name('enablements.approve');
        Route::post('/enablements/{id}/reject', [AdminExclusiveContentController::class, 'rejectEnablement'])->name('enablements.reject');
        
        Route::get('/pending-content', [AdminExclusiveContentController::class, 'listPendingContent'])->name('pending.list');
        Route::post('/pending-content/{id}/approve', [AdminExclusiveContentController::class, 'approveContent'])->name('pending.approve');
        Route::post('/pending-content/{id}/reject', [AdminExclusiveContentController::class, 'rejectContent'])->name('pending.reject');
        
        Route::post('/refunds/{purchaseId}', [AdminExclusiveContentController::class, 'issueRefund'])->name('refunds');

        // Wallet Visibility API hooks
        Route::get('/wallets', [AdminWalletController::class, 'listWallets'])->name('wallets.list');
        Route::get('/wallets/stats', [AdminWalletController::class, 'revenueStats'])->name('wallets.stats');
        Route::get('/wallets/transactions', [AdminWalletController::class, 'allTransactions'])->name('wallets.transactions');
    });
});

// =====================
// Testing / Dev Routes
// =====================

Route::get('/test-s3', function () {
    try {
        Storage::disk('s3')->put('test.txt', 'Hello from Laravel S3!');
        return '✅ File uploaded to real S3 bucket!';
    } catch (\Exception $e) {
        return '❌ Upload failed: ' . $e->getMessage();
    }
});

Route::get('/check-disk', fn() => config('filesystems.default'));

// =====================
// Temporary Payment Test Routes
// =====================
Route::get('/temp-payment-test', function() {
    if (\App\Models\SubscriptionPlan::count() === 0) {
        try {
            \App\Models\SubscriptionPlan::create([
                'profile_type' => 'personal',
                'name' => 'Test Premium Plan',
                'slug' => 'test-premium-plan',
                'description' => 'Test premium subscription plan',
                'price' => 10.00,
                'currency' => 'INR',
                'duration_days' => 30,
                'features' => [],
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 1,
                'gateway_plan_id' => 'test_plan_id_123',
            ]);
        } catch (\Exception $e) {}
    }
    $plans = \App\Models\SubscriptionPlan::all();
    return view('temp_payment_test', compact('plans'));
})->name('temp-payment.test');

Route::post('/temp-payment-test/pay', function(\Illuminate\Http\Request $request) {
    $mode = $request->input('mode', 'purchase');
    try {
        if ($mode === 'purchase') {
            $amount = (float) $request->input('amount', 10);
            $dto = new \App\Services\Payment\DTOs\PaymentIntentDTO(
                amount: $amount,
                currency: 'INR',
                description: 'Temp Test Purchase',
                customerId: 'CUST_' . uniqid(),
                customerEmail: $request->input('email', 'test@example.com'),
                customerPhone: $request->input('phone', '9999999999'),
                orderId: 'ORD_' . uniqid()
            );
            $result = app(\App\Services\Payment\GatewayManager::class)->driver('phonepe')->createPaymentIntent($dto);
        } else {
            $planId = $request->input('plan_id');
            $plan = \App\Models\SubscriptionPlan::findOrFail($planId);
            $result = app(\App\Services\Payment\GatewayManager::class)->driver('phonepe')->createSubscription(
                customerId: 'CUST_' . uniqid(),
                planId: $plan->gateway_plan_id
            );
        }
        if ($result->success && !empty($result->redirectUrl)) {
            return redirect()->away($result->redirectUrl);
        }
        return back()->with('error', 'Payment Initiation Failed: ' . ($result->message ?? 'Unknown Error'));
    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
})->name('temp-payment.pay');

// Auth scaffolding (Laravel Breeze / Jetstream / Fortify)
require __DIR__ . '/auth.php';
