<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TourController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::get('/tours', [TourController::class, 'index']);
Route::get('/tours/{slug}', [TourController::class, 'show']);

Route::post('/bookings', [App\Http\Controllers\Api\BookingController::class, 'store']);
Route::post('/fix-departure', [App\Http\Controllers\Api\FixDepartureController::class, 'store']);

Route::post('/plan-your-trip', [App\Http\Controllers\Api\PlanYourTripController::class, '__invoke'])->middleware('throttle:5,1');
Route::post('/contact', [App\Http\Controllers\Api\ContactController::class, 'store'])->middleware('throttle:5,1');

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,20');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,20');
Route::post('/auth/verify-code', [AuthController::class, 'verifyCode'])->middleware('throttle:5,20');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,20');

Route::get('/blog/posts', [App\Http\Controllers\Api\BlogController::class, 'index']);
Route::get('/blog/posts/{slug}', [App\Http\Controllers\Api\BlogController::class, 'show']);
Route::get('/blog/categories', [App\Http\Controllers\Api\BlogController::class, 'categories']);

Route::get('/cms/pages', [App\Http\Controllers\Api\CmsController::class, 'index']);
Route::get('/cms/pages/{slug}', [App\Http\Controllers\Api\CmsController::class, 'show']);

Route::get('/faqs', [App\Http\Controllers\Api\FaqController::class, 'index']);

Route::get('/regions', [App\Http\Controllers\Api\RegionController::class, 'index']);
Route::get('/regions/{slug}', [App\Http\Controllers\Api\RegionController::class, 'show']);

Route::get('/category-page-settings', [App\Http\Controllers\Api\CategoryPageSettingController::class, 'index']);

Route::get('/site-settings', [App\Http\Controllers\Api\SiteSettingController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Authenticated API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::patch('/auth/profile', [AuthController::class, 'updateProfile']);
});

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', EnsureAdmin::class])->prefix('admin')->group(function (): void {
    Route::get('/dashboard', [App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);

    Route::apiResource('tours', App\Http\Controllers\Api\Admin\TourController::class);
    Route::apiResource('blog-categories', App\Http\Controllers\Api\Admin\BlogCategoryController::class);
    Route::apiResource('blog-posts', App\Http\Controllers\Api\Admin\BlogPostController::class);
    Route::post('blog-posts/bulk-delete', [App\Http\Controllers\Api\Admin\BlogPostController::class, 'bulkDestroy']);
    Route::apiResource('pages', App\Http\Controllers\Api\Admin\PageController::class);
    Route::apiResource('bookings', App\Http\Controllers\Api\Admin\BookingController::class);
    Route::apiResource('plan-your-trip', App\Http\Controllers\Api\Admin\PlanYourTripController::class);
    Route::apiResource('contact-submissions', App\Http\Controllers\Api\Admin\ContactSubmissionController::class);
    Route::apiResource('faqs', App\Http\Controllers\Api\Admin\FaqController::class);
    Route::apiResource('regions', App\Http\Controllers\Api\Admin\RegionController::class);
    Route::apiResource('users', App\Http\Controllers\Api\Admin\UserController::class);

    Route::get('site-settings', [App\Http\Controllers\Api\Admin\SiteSettingController::class, 'index']);
    Route::get('site-settings/{key}', [App\Http\Controllers\Api\Admin\SiteSettingController::class, 'show']);
    Route::put('site-settings/{key}', [App\Http\Controllers\Api\Admin\SiteSettingController::class, 'update']);
    Route::post('site-settings/bulk-update', [App\Http\Controllers\Api\Admin\SiteSettingController::class, 'bulkUpdate']);

    Route::get('category-page-settings', [App\Http\Controllers\Api\Admin\CategoryPageSettingController::class, 'index']);
    Route::put('category-page-settings/{category}', [App\Http\Controllers\Api\Admin\CategoryPageSettingController::class, 'update']);

    Route::get('/media', [App\Http\Controllers\Api\Admin\MediaController::class, 'index']);
    Route::post('/media', [App\Http\Controllers\Api\Admin\MediaController::class, 'store']);
    Route::post('/media/bulk', [App\Http\Controllers\Api\Admin\MediaController::class, 'bulkStore']);
    Route::delete('/media/{media}', [App\Http\Controllers\Api\Admin\MediaController::class, 'destroy']);
});
