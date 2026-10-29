<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\DynamicGalleryController;
use App\Http\Controllers\Api\DynamicPageApiController;
use App\Http\Controllers\Api\FaceAnalysisController;
use App\Http\Controllers\Api\FaceMetricController;
use App\Http\Controllers\Api\PremiumController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\SystemSettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideoTutorialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/

Route::post('/social-login', [SocialLoginController::class, 'socialLogin']);
Route::post('/login-with-user-id', [SocialLoginController::class, 'loginWithUserId']);

Route::get('/pages', [DynamicPageApiController::class, 'index']);
Route::get('/pages/{slug}', [DynamicPageApiController::class, 'show']);
Route::get('/dynamic-galleries', [DynamicGalleryController::class, 'index']);
Route::get('/dynamic-galleries/{id}', [DynamicGalleryController::class, 'show']);


Route::controller(RegisterController::class)->prefix('users/register')->group(function () {
    Route::post('/', 'userRegister');
    Route::post('/otp-verify', 'otpVerify');
    Route::post('/otp-resend', 'otpResend');
    Route::post('/email-exists', 'emailExists');
});

Route::controller(LoginController::class)->prefix('users/login')->group(function () {
    Route::post('/', 'userLogin');
    Route::post('/email-verify', 'emailVerify');
    Route::post('/otp-resend', 'otpResend');
    Route::post('/otp-verify', 'otpVerify');
    Route::post('/reset-password', 'resetPassword');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // System settings
    Route::controller(SystemSettingController::class)->group(function () {
        Route::get('/site-settings', 'index');
    });

    // Social media links
    Route::controller(SocialMediaController::class)->group(function () {
        Route::get('/social-links', 'index');
    });

    // Video tutorials
    Route::controller(VideoTutorialController::class)->group(function () {
        Route::get('/video-tutorials', 'index');
    });

    // Dynamic gallery
    Route::controller(DynamicGalleryController::class)->group(function () {
        Route::get('/dynamic-galleries', 'index');
        Route::get('/dynamic-galleries/{id}', 'show');
    });
    // User account
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('/', 'userDetails');
        Route::post('/update', 'updateUser');
        Route::post('/update-password', 'updatePassword');
        Route::delete('/delete-account', 'deleteAccount');
        Route::post('/logout', 'logoutUser');
        Route::post('/onboarding', [SocialLoginController::class, 'completeOnboarding']);
    });

    // Face scan — analysis, overview, card, region breakdown
    // NOTE: '/face-scan/my-scans' must stay above '/face-scan/{faceScan}',
    // otherwise Laravel matches it as {faceScan} = "my-scans".
    Route::controller(FaceAnalysisController::class)->prefix('face-scan')->group(function () {
        Route::post('/', 'store');
        Route::get('/my-scans', 'myScans');
        Route::get('/{faceScan}', 'show');
        Route::get('/{faceScan}/analyze', 'analyze');
        Route::post('/{faceScan}/card', 'generateCard');
        Route::get('/{faceScan}/comparison', 'comparison');
    });

    // Face scan — per-metric detail modal
    Route::controller(FaceMetricController::class)->prefix('face-scan/{faceScan}/metrics')->group(function () {
        Route::get('/', 'index');
        Route::get('/{key}', 'show');
    });
    Route::post('/premium/redeem', [PremiumController::class, 'redeem'])->middleware('throttle:5,1');
    Route::get('/premium/history', [PremiumController::class, 'history']);

});
