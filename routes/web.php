<?php

use App\Http\Controllers\Auth\AuthSessionController;
use App\Http\Controllers\Buying\CartController;
use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\StorageManager;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Route;

Route::view('/debug', '__');
Route::get('/test', function () {
    $products = Product::orderBy('id', 'desc')->paginate(10);
    $products_1 = Product::all();
    $products_2 = Product::where('category_id', '1')->get();
    $product_3 = Product::with('category')->where('title', 'like', '%men%')->get();
    dd($products, $products_1, $products_2, $product_3, $product_3->toArray(), $product_3->toJson());
});

Route::get('/test-2',function(){
    $categories = ProductCategory::with('products.category')->get();
    $categories->transform(function($category){
        return $category->toArray();
    });
    return dd($categories);
});

// User Routes

Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'showCartList')->name('cart.get');
    Route::post('/cart/{id}', 'addQuantity')->name('cart.id.post');
    Route::put('/cart/{id}', 'removeQuantity')->name('cart.id.put');
    Route::delete('/cart/{id}', 'removeCartItem')->name('cart.id.delete');
    Route::get('/cart/clear', 'clearCartItems')->name('cart.clear.get');
});

Route::controller(AuthSessionController::class)->group(function () {
    Route::get('/register',  'showRegister')->name('register.get');
    Route::get('/login',  'showLogin')->name('login');
    Route::get('/profile',  'showProfile')->middleware('auth')->name('profile.get');

    Route::post('/register', 'register')->name('register.post');
    Route::post('/login',  'login')->name('login.post');
    Route::post('/logout',  'logout')->name('logout.post');

    Route::post('/profile', 'updateProfile')->middleware('auth')->name('profile.post');
});


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

//////////////////////////////////////////////////////////////////////////

// Storage Manager

Route::middleware('auth')->group(function () {
    Route::get('/storage-manager', [StorageManager::class, 'show']);
    Route::get('/storage-manager/list', [StorageManager::class, 'list']);
    Route::delete('/storage-manager/delete', [StorageManager::class, 'delete']);
    Route::post('/storage-manager/rename', [StorageManager::class, 'rename']);
    Route::post('/storage-manager/upload', [StorageManager::class, 'upload']);
    Route::post('/storage-manager/create-folder', [StorageManager::class, 'createFolder']);
});

//////////////////////////////////////////////////////////////////////////

// Admin Routes
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

        Route::controller(ProductCategoryController::class)->group(function () {
            Route::get('/category', 'showCategories')->name('admin.dashboard.category.get');
            Route::get('/category/add', 'showAddCategory')->name('admin.dashboard.category.add.get');
            Route::get('/category/edit/{id}/', 'showEditCategory')->name('admin.dashboard.category.edit.id.get');
            Route::post('/category', 'addCategory')->name('admin.dashboard.category.post');
            Route::post('/category/{id}', 'updateCategory')->name('admin.dashboard.category.id.post');
            Route::delete('/category/{id}', 'deleteCategory')->name('admin.dashboard.category.id.delete');
        });
    });
});
