<?php

use App\Article\Controllers\ArticleController;
use App\Auth\Controllers\NotificationController;
use App\Auth\Controllers\WishlistController;
use App\Cart\Controllers\CartController;
use App\ContactMessage\Controllers\ContactMessageController;
use App\FAQ\Controllers\FAQController;
use App\Inventory\Controllers\CouponController;
use App\Inventory\Controllers\ProductController;
use App\Inventory\Controllers\ProductVariantController;
use App\Order\Controllers\OrderController;
use App\Payment\Controllers\PaymentController;
use App\Payment\Controllers\PaymentMethodController;
use App\Review\Controllers\ProductReviewController;
use App\Review\Controllers\ProductReviewReplyController;
use App\Setting\Models\AppSetting;
use App\Shipping\Controllers\ShippingMethodController;
use App\Store\Controllers\StoreBranchController;
use App\Tax\Controllers\TaxRateController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.user.core.landing')->name('home.get');

Route::view('/setting', 'pages.user.dashboard.general_setting')->name('setting.get');
Route::view('/privacy', 'pages.user.core.privacy')->name('privacy.get');
Route::view('/terms', 'pages.user.core.terms')->name('terms.get');
Route::view('/about', 'pages.user.core.about_us')->name('about_us.get');
Route::view('/contact', 'pages.user.core.contact')->name('contact.get');
Route::post('/contact', [ContactMessageController::class, 'createMessage'])->name('contact.post');
Route::get('/frequent-questions', [FAQController::class, 'viewFAQPage'])->name('frequent-questions.get');

Route::get('/articles/search', [ArticleController::class, 'viewUserArticleSearchListPage'])->name('articles.search.get');
Route::get('/articles/{slug}', [ArticleController::class, 'viewUserArticleDetailPage'])->name('articles.slug.get');
Route::get('/articles', [ArticleController::class, 'viewUserArticleListPage'])->name('articles.get');


Route::controller(NotificationController::class)->group(function () {
    Route::get('/notification', 'getNotifications')->name('notification.get')->middleware('auth');

    Route::delete('/notification/{id}', 'deleteNotification')->name('notification.id.delete')->middleware('auth');

    Route::post('/notification/mark-read', 'markReadNotifications')
        ->name('notification.mark_read')
        ->middleware('auth');

    Route::get('/notification/unread-count', 'getUnreadCount')
        ->name('notification.unread_count')
        ->middleware('auth');
});

Route::controller(CartController::class)->group(function () {
    Route::get('/cart/items', 'getCartItems')->name('cart.items.get')->middleware('auth');
    Route::post('/cart/add', 'addToCart')->name('cart.add.post')->middleware('auth');
    Route::post('/cart/remove', 'removeFromCart')->name('cart.remove.post')->middleware('auth');
    Route::post('/cart/clear', 'clearCart')->name('cart.clear.post')->middleware('auth');
    Route::get('/cart', 'viewUserCartPage')->name('cart.get')->middleware('auth');
});

Route::controller(WishlistController::class)->group(function () {
    Route::get('/wishlist', 'getWishlists')->name('wishlist.get')->middleware('auth');

    Route::get('/wishlist/api', 'getAPIWishlists')->name('wishlist.api.get')->middleware('auth');

    Route::post('/wishlist', 'toggleWishlist')->name('wishlist.post')->middleware('auth');

    Route::delete('/wishlist/{id}', 'deleteWishlist')->name('wishlist.id.delete')->middleware('auth');
});



Route::controller(StoreBranchController::class)->group(function () {
    Route::get('/store-locator', 'viewUserStoreLocatorPage');
});

Route::controller(ProductReviewReplyController::class)->group(function () {
    Route::post('/shop/review/{review_id}/reply', 'createReviewReply')
        ->name('shop.review.review_id.reply.post')->middleware('auth');

    Route::delete('/shop/review/{review_id}/reply/{reply_id}', 'deleteReviewReply')
        ->name('shop.review.review_id.reply.reply_id.delete')->middleware('auth');
});


Route::controller(ProductReviewController::class)->group(function () {
    Route::post('/shop/review', 'createReview')
        ->name('shop.review.post')->middleware('auth');

    Route::get('/shop/{product_id}/reviews', 'fetchProductReviews')
        ->name('shop.product_id.reviews.get');

    Route::delete('/shop/review/{id}', 'deleteReview')
        ->name('shop.review.id.delete')->middleware('auth');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/shop/api/popular-products', 'fetchPopularProductsByAPI');
    Route::get('/shop/api/pinned-products', 'fetchPinnedProductsByAPI');
    Route::get('/shop/api/promotion-products', 'fetchPromotionProductsByAPI');
    Route::get('/shop/search/category/{category}', 'showSearchProductListByCategoryUser');
    Route::get('/shop/search/tag/{tag}', 'showSearchProductListByTagUser');
    Route::get('/shop/search/brand/{brand}', 'showSearchProductListByBrandUser');
    Route::get('/shop/search', 'showSearchProductListUser');
    Route::get('/shop', 'showProductListUser')->name('shop.get');

    Route::get('/shop/{slug}', 'showProductDetail')->name('shop.slug.get');
});

Route::controller(CouponController::class)->group(function () {
    Route::post('/order/check-coupon', 'checkCoupon')->name('order.apply-coupon.post');
});

Route::controller(ProductVariantController::class)->group(function () {
    Route::post('/product/variant/check-variant-stock',  'checkVariantStock');
});

Route::controller(ShippingMethodController::class)->group(function () {
    Route::post('/filter-shipping-method', 'filterShippingMethod')->name('filter-shipping-method.post');
});

Route::controller(TaxRateController::class)->group(function () {
    Route::post('/calculate-tax', 'calculateTaxCost')->name('calculate.tax.post');
});

Route::controller(PaymentMethodController::class)->group(function () {
    Route::post('/filter-payment-method', 'filterPaymentMethod')->name('filter-payment-method.post');
});

Route::controller(OrderController::class)->group(function () {
    Route::get('/checkout', 'viewUserCheckOutPage')->name('checkout.get')->middleware('auth');

    Route::post('/checkout',  'createOrder')->name('checkout.post');

    Route::get('/order-history', 'viewUserOrderHistory')->name('order_history.get')->middleware('auth');

    Route::get('/order/{order_id}/invoice/{invoice_id}/view', 'viewOrderInvoice')
        ->name('order.id.invoice.id.view.get')->middleware('auth');

    Route::get('/order/{order_id}/invoice/{invoice_id}/download', 'downloadOrderInvoice')
        ->name('order.id.invoice.id.download.get')->middleware('auth');

    Route::get('/order/{id}', 'viewUserOrderHistoryDetail')->name('order_detail.id.get')->middleware('auth');

    Route::delete('/order-history/{id}', 'deleteOrder')->name('order_history.delete')->middleware('auth');
});


Route::controller(PaymentController::class)->group(function () {
    Route::get('/payment/{invoice_id}', 'viewUserInvoicePage')
        ->name('payment.id.get')->middleware('auth');
    Route::get('/payment', 'viewUserInvoiceListPage')->name('payment.get');
});
