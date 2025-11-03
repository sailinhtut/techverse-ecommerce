<?php

use App\Core\Controllers\StorageManager;
use App\Inventory\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/setting', 'pages.user.dashboard.general_setting')->name('setting.get');
Route::view('/privacy', 'pages.user.core.privacy')->name('privacy.get');
Route::view('/terms', 'pages.user.core.terms')->name('terms.get');
Route::view('/about', 'pages.user.core.about_us')->name('about_us.get');
Route::view('/contact', 'pages.user.core.contact')->name('contact.get');
Route::view('/cart', 'pages.user.core.cart')->name('cart.get');


Route::middleware('auth')->group(function () {
    Route::get('/storage-manager', [StorageManager::class, 'show']);
    Route::get('/storage-manager/list', [StorageManager::class, 'list']);
    Route::delete('/storage-manager/delete', [StorageManager::class, 'delete']);
    Route::post('/storage-manager/rename', [StorageManager::class, 'rename']);
    Route::post('/storage-manager/upload', [StorageManager::class, 'upload']);
    Route::post('/storage-manager/create-folder', [StorageManager::class, 'createFolder']);
});
