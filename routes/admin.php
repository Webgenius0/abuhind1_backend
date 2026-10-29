<?php

use App\Http\Controllers\Web\Backend\DashBoardController;
use App\Http\Controllers\Web\Backend\DynamicGalleryController;
use App\Http\Controllers\Web\Backend\DynamicPageController;
use App\Http\Controllers\Web\Backend\IntegrationSettingController;
use App\Http\Controllers\Web\Backend\MailSettingController;
use App\Http\Controllers\Web\Backend\PremiumCodeController;
use App\Http\Controllers\Web\Backend\ProfileController;
use App\Http\Controllers\Web\Backend\SocialMediaController;
use App\Http\Controllers\Web\Backend\SystemSettingController;
use App\Http\Controllers\Web\Backend\UserController;
use App\Http\Controllers\Web\Backend\VideoTutorialController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashBoardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::match(['get', 'post'], '/status/{id}', 'status')->name('status');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});

Route::controller(SystemSettingController::class)->group(function () {
    Route::get('/system-setting', 'index')->name('system.index');
    Route::post('/system-setting', 'update')->name('system.update');
});

Route::controller(MailSettingController::class)->prefix('mail-setting')->name('mail.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'update')->name('update');
    Route::post('/test', 'test')->name('test');
});

Route::controller(IntegrationSettingController::class)->group(function () {
    Route::get('/integration-setting', 'index')->name('integration.index');
    Route::patch('/google-setting', 'updateGoogle')->name('integration.google');
    Route::patch('/apple-setting', 'updateApple')->name('integration.apple');
    Route::patch('/gemini-setting', 'updateGemini')->name('integration.gemini');
});

Route::controller(SocialMediaController::class)->prefix('social-links')->name('social.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('delete');
});

Route::controller(ProfileController::class)->group(function () {
    Route::post('/update-profile-picture', 'UpdateProfilePicture')->name('update.profile.picture');
    Route::post('/update-profile-password', 'UpdatePassword')->name('update.Password');

    Route::get('/profile', 'showProfile')->name('profile.setting');
    Route::post('/update-profile', 'UpdateProfile')->name('update.profile');
});

Route::controller(DynamicPageController::class)->prefix('pages')->name('dynamic_page.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::match(['get', 'post'], '/status/{id}', 'status')->name('status');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});

Route::controller(VideoTutorialController::class)->group(function () {
    Route::get('/video-tutorial', 'index')->name('video_tutorial.index');
    Route::get('/video-tutorial/create', 'create')->name('video_tutorial.create');
    Route::post('/video-tutorial/store', 'store')->name('video_tutorial.store');
    Route::get('/video-tutorial/edit/{id}', 'edit')->name('video_tutorial.edit');
    Route::post('/video-tutorial/update/{id}', 'update')->name('video_tutorial.update');
    Route::match(['get', 'post'], '/video-tutorial/status/{id}', 'status')->name('video_tutorial.status');
    Route::delete('/video-tutorial/destroy/{id}', 'destroy')->name('video_tutorial.destroy');
});

Route::controller(DynamicGalleryController::class)->group(function () {
    Route::get('/dynamic-gallery', 'index')->name('dynamic_gallery.index');
    Route::get('/dynamic-gallery/create', 'create')->name('dynamic_gallery.create');
    Route::post('/dynamic-gallery/store', 'store')->name('dynamic_gallery.store');
    Route::get('/dynamic-gallery/edit/{id}', 'edit')->name('dynamic_gallery.edit');
    Route::post('/dynamic-gallery/update/{id}', 'update')->name('dynamic_gallery.update');
    Route::match(['get', 'post'], '/dynamic-gallery/status/{id}', 'status')->name('dynamic_gallery.status');
    Route::delete('/dynamic-gallery/destroy/{id}', 'destroy')->name('dynamic_gallery.destroy');
});

Route::controller(PremiumCodeController::class)->prefix('premium-code')->name('premium_code.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::match(['get', 'post'], '/status/{id}', 'status')->name('status');
    Route::delete('/{id}', 'destroy')->name('destroy');
});
