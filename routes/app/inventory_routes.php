<?php

use App\Http\Controllers\Product\ProductController;
use App\Inventory\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('/admin')->group(function () {
    Route::prefix('/dashboard')->group(function () {
        Route::view('/', 'pages.admin.dashboard.leaderboard')->name('admin.dashboard.get');
        Route::view('/media-storage', 'pages.admin.dashboard.storage_manager')->name('admin.media_storage.get');

        Route::controller(ProductController::class)->group(function () {
            Route::get('/product', 'showProductListAdmin')->name('admin.dashboard.product.get');
            Route::get('/product/add', 'showAddProduct')->name('admin.dashboard.product.add.get');
            Route::get('/product/edit/{id}/', 'showEditProduct')->name('admin.dashboard.product.edit.id.get');
            Route::post('/product', 'addProduct')->name('admin.dashboard.product.post');
            Route::post('/product/{id}', 'updateProduct')->name('admin.dashboard.product.id.post');
            Route::delete('/product/{id}', 'deleteProduct')->name('admin.dashboard.product.id.delete');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/category', 'showCategories')->name('admin.dashboard.category.get');
            Route::get('/category/add', 'showAddCategory')->name('admin.dashboard.category.add.get');
            Route::get('/category/edit/{id}/', 'showEditCategory')->name('admin.dashboard.category.edit.id.get');
            Route::post('/category', 'addCategory')->name('admin.dashboard.category.post');
            Route::post('/category/{id}', 'updateCategory')->name('admin.dashboard.category.id.post');
            Route::delete('/category/{id}', 'deleteCategory')->name('admin.dashboard.category.id.delete');
        });
    });
});
