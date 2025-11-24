<?php

use App\Auth\Controllers\AddressController;
use App\Auth\Controllers\AuthController;
use App\Auth\Controllers\NotificationController;
use App\Auth\Controllers\UserController;
use App\Auth\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {


    Route::post('/email/resend', 'sendVerificationEmail')->name('email.resend.post')->middleware(['auth']);

    Route::get('/email/verify/{id}/{hash}', 'verifyEmail')->name('email.verify.get')->middleware(['signed']);

    Route::get('/forgot-password', 'showForgotPassword')->name('forgot-password.get');

    Route::post('/forgot-password', 'sendForgotPasswordEmail')->name('forgot-password.post');

    Route::get('/reset-password', 'showResetPassword')
        ->name('reset-password.get');

    Route::post('/reset-password', 'resetPassword')
        ->name('reset-password.post');

    Route::get('/register',  'showRegister')->name('register.get');
    Route::get('/login',  'showLogin')->name('login');

    Route::post('/register', 'register')->name('register.post');
    Route::post('/login',  'login')->name('login.post');
    Route::post('/logout',  'logout')->name('logout.post');
});

Route::controller(UserController::class)->group(function () {

    Route::get('/profile', 'showProfile')->name('profile.get')->middleware('auth');

    Route::post('/profile/update/{id}', 'updateProfile')->name('profile.update.post');

    Route::delete('/profile/delete/{id}', 'deleteProfile')->name('profile.delete.delete')->middleware('auth');
});

Route::controller(NotificationController::class)->group(function () {
    Route::get('/notification', 'getNotifications')->name('notification.get')->middleware('auth');

    Route::delete('/notification/{id}', 'deleteNotification')->name('notification.id.delete')->middleware('auth');
});

Route::controller(AddressController::class)->group(function () {
    Route::get('/address', 'getAddresses')->name('address.get')->middleware('auth');

    Route::post('/address', 'createAddress')->name('address.post')->middleware('auth');

    Route::post('/address/{id}', 'updateaddress')->name('address.id.post')->middleware('auth');

    Route::delete('/address/{id}', 'deleteAddress')->name('address.id.delete')->middleware('auth');
});

