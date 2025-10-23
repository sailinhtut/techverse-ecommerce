<?php

use App\Inventory\Controllers\ProductVariantController;
use App\Order\Controllers\OrderController;
use App\Payment\Controllers\PaymentController;
use App\Payment\Controllers\PaymentMethodController;
use App\Shipping\Controllers\ShippingMethodController;
use App\Tax\Controllers\TaxRateController;
use Illuminate\Support\Facades\Route;


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

    Route::delete('/order-history/{id}', 'deleteOrder')->name('order_history.delete')->middleware('auth');
});


Route::controller(PaymentController::class)->group(function () {
    Route::get('/payment', 'viewUserInvoiceListPage')->name('payment.get');
});
