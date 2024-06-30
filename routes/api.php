<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RequestOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\UserPromoCodeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewImageController;
use App\Http\Controllers\DashboardController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/request-orders', [RequestOrderController::class, 'adminIndex']);
        Route::put('/admin/request-orders/{id}/status/{status}', [RequestOrderController::class, 'updateStatus']);

        Route::post('/promo-codes/apply', [PromoCodeController::class, 'apply']);
        Route::post('/promo-codes/{promoCodeId}/assign', [UserPromoCodeController::class, 'assignToUsers']);
        Route::get('promo-codes/{promoCodeId}/assigned-users', [UserPromoCodeController::class, 'getAssignedUsers']);
        
        Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStats']);
        Route::get('/dashboard/earning/weekly', [DashboardController::class, 'getWeeklyEarnings']);
        Route::get('/dashboard/top-users', [DashboardController::class, 'getTopUsers']);
        Route::get('/dashboard/top-services', [DashboardController::class, 'getTopServices']);
        Route::get('/dashboard/statistics/general', [DashboardController::class, 'getGeneralPromoCodeStatistics']);
        Route::get('/dashboard/statistics/detailed', [DashboardController::class, 'getDetailedPromoCodeStatistics']);
    });
    Route::apiResource('services', ServiceController::class);

    Route::apiResource('users', UserController::class);
    Route::post('/users/change-password', [UserController::class, 'changePassword']);
    Route::post('/logout', [UserController::class, 'logout']);
    
    Route::resource('orders', OrderController::class);
    Route::get('/user/{id}/orders', [OrderController::class, 'getUserRequests']);

    Route::apiResource('request-orders', RequestOrderController::class);
   
    Route::get('/user/{id}/request-orders', [RequestOrderController::class, 'getUserRequests']);

    Route::get('/user', function (Request $request) { return $request->user(); });

    Route::apiResource('promo-codes', PromoCodeController::class);
   
    Route::get('/users/{userId}/promo-codes', [PromoCodeController::class, 'getUserPromoCodes']);

    

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

    Route::post('/reviews', [ReviewController::class, 'storeReview']);
    Route::get('/reviews/{userId}/summary', [ReviewController::class, 'getReviewSummary']);
    Route::get('/reviews/{userId}/pending', [ReviewController::class, 'getPendingReviews']);
    Route::post('/reviews/{reviewId}/submit', [ReviewController::class, 'submitReview']);
    Route::get('/reviews/{userId}/completed', [ReviewController::class, 'getCompletedReviews']);
    Route::get('/reviews/{userId}/stats', [ReviewController::class, 'getReviewStats']);
    Route::get('/reviews/service/{serviceId}', [ReviewController::class, 'getReviewByService']);

   
});

// Các routes đăng nhập và đăng ký không cần xác thực
Route::post('login', [UserController::class,'login']);
Route::post('register', [UserController::class,'register']);
Route::get('/email/verify/{id}', [UserController::class, 'verify'])->name('verification.verify');

