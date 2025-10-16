<?php

use App\Core\Controllers\StorageManager;
use App\Inventory\Controllers\ProductController;
use App\Order\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderController::class)->group(function () {

    Route::get('/checkout', 'viewUserCheckOutPage')->name('checkout.get')->middleware('auth');

    Route::post('/checkout',  'createOrder')->name('checkout.post');

    Route::get('/order-history', 'viewUserOrderHistory')->name('order_history.get')->middleware('auth');

    Route::delete('/order-history/{id}', 'deleteOrder')->name('order_history.delete')->middleware('auth');
});
