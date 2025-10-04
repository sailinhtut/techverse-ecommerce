<?php

use App\Http\Controllers\Auth\AuthTokenController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Middleware\TestMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// Product API
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'getProducts')->name('api.products.get');
    Route::post('/products', 'addProduct')->name('api.products.post');
    Route::post('/products/{id}', 'updateProduct')->name('api.products.id.post');
    Route::delete('/products/{id}', 'deleteProduct')->name('api.products.id.delete');
});
