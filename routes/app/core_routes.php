<?php

use App\Core\Controllers\StorageManager;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.user.core.landing')->name('home.get');
Route::get('/shop', [ProductController::class, 'showProductListUser'])->name('shop.get');
Route::get('/shop/{slug}', [ProductController::class, 'showProductDetail'])->name('shop.slug.get');
Route::view('/wish-list', 'pages.user.dashboard.wishlist')->name('wish_list.get');
Route::view('/order-history', 'pages.user.dashboard.order_history')->name('order_history.get');
Route::view('/shipping-address', 'pages.user.dashboard.shipping_address')->name('shipping_address.get');
Route::view('/payment-transaction', 'pages.user.dashboard.payment_transaction')->name('payment_transaction.get');
Route::view('/setting', 'pages.user.dashboard.general_setting')->name('setting.get');
Route::view('/privacy', 'pages.user.core.privacy')->name('privacy.get');
Route::view('/terms', 'pages.user.core.terms')->name('terms.get');
Route::view('/about', 'pages.user.core.about_us')->name('about_us.get');
Route::view('/contact', 'pages.user.core.contact')->name('contact.get');


Route::middleware('auth')->group(function () {
    Route::get('/storage-manager', [StorageManager::class, 'show']);
    Route::get('/storage-manager/list', [StorageManager::class, 'list']);
    Route::delete('/storage-manager/delete', [StorageManager::class, 'delete']);
    Route::post('/storage-manager/rename', [StorageManager::class, 'rename']);
    Route::post('/storage-manager/upload', [StorageManager::class, 'upload']);
    Route::post('/storage-manager/create-folder', [StorageManager::class, 'createFolder']);
});
