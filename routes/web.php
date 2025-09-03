<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
    Route::get('/dashboard/chart/{type}/{days?}', [DashboardController::class, 'chart'])->name('dashboard.chart');
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});




Route::get('/test-s3', function () {
    try {
        Storage::disk('s3')->put('test.txt', 'Hello from Laravel S3!');
        return '✅ File uploaded to real S3 bucket!';
    } catch (\Exception $e) {
        return '❌ Upload failed: ' . $e->getMessage();
    }
});

Route::get('/check-disk', function () {
    return config('filesystems.default');
});


require __DIR__ . '/auth.php';
