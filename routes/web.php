<?php

use App\Http\Controllers\Admin\Ecommerce\ProductController;
use App\Http\Controllers\Admin\Ecommerce\ServiceController;
use App\Http\Controllers\Admin\Ecommerce\AdminJobController;
use App\Http\Controllers\Admin\Ecommerce\AdminApplicationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\Ecommerce\OrderController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\FollowController;
use App\Http\Controllers\Admin\UserController;

// Public Routes
// Route::get('/', fn() => view('welcome'));
Route::get('/', fn() => redirect('/admin'));
Route::get('/admin', fn() => view('auth.login'));

// Dashboard (User)
Route::middleware(['auth', 'verified'])->get('/dashboard', fn() => view('dashboard'))->name('dashboard');

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

    // Followers / Following
    Route::controller(FollowController::class)->prefix('user')->name('user.')->group(function () {
        Route::get('{userId}/followers', 'getFollowers')->name('follower');
        Route::get('{userId}/following', 'getFollowing')->name('following');
    });

    // Posts Management (Admin)
    Route::prefix('posts')->controller(PostController::class)->name('post.')->group(function () {
        Route::get('/', 'index')->name('index');
        // Route::get('/{id}', 'showPostDetails')->name('show');
        Route::get('/{id}/likes', 'showLikes')->name('likes');
        Route::get('/{id}/comments', 'showComments')->name('comments');
    });

    // User Posts List
    Route::get('/user-posts', [AdminPostController::class, 'index'])->name('userposts.index');
    Route::get('/user-posts/{postId}', [AdminPostController::class, 'show'])->name('userpost.details');


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
    // Services Management (Admin)
    // =====================

    // Route::prefix('job')->controller(JobController::class)->name('job.')->group(function () {
    //     Route::get('/', 'index')->name('index');
    //     Route::get('/{job}', 'show')->name('show');
    //     Route::post('/{job}/approve', 'approve')->name('approve');
    //     Route::post('/{job}/reject', 'reject')->name('reject');
    //     Route::post('/bulk/approve', 'bulkApprove')->name('bulk.approve');
    //     Route::post('/bulk/reject', 'bulkReject')->name('bulk.reject');
    // });
    // Job Management Routes
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

// Auth scaffolding (Laravel Breeze / Jetstream / Fortify)
require __DIR__ . '/auth.php';
