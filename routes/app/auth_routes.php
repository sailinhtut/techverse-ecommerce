<?php

use App\Auth\Controllers\AuthSessionController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthSessionController::class)->group(function () {
    Route::get('/register',  'showRegister')->name('register.get');
    Route::get('/login',  'showLogin')->name('login');
    Route::get('/profile',  'showProfile')->middleware('auth')->name('profile.get');

    Route::post('/register', 'register')->name('register.post');
    Route::post('/login',  'login')->name('login.post');
    Route::post('/logout',  'logout')->name('logout.post');

    Route::post('/profile', 'updateProfile')->middleware('auth')->name('profile.post');
});
