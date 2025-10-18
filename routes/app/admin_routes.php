<?php

use App\Auth\Controllers\UserController;
use App\Inventory\Controllers\BrandController;
use App\Inventory\Controllers\CategoryController;
use App\Inventory\Controllers\ProductController;
use App\Inventory\Controllers\ProductVariantAttributeController;
use App\Inventory\Models\ProductVariantAttribute;
use App\Order\Controllers\OrderController;
use App\Payment\Controllers\InvoiceController;
use App\Payment\Controllers\PaymentController;
use App\Payment\Controllers\PaymentMethodController;
use App\Payment\Controllers\TransactionController;
use App\Shipping\Controllers\ShippingClassController;
use App\Shipping\Controllers\ShippingMethodController;
use App\Shipping\Controllers\ShippingRateController;
use App\Shipping\Controllers\ShippingZoneController;
use App\Tax\Controllers\TaxClassController;
use App\Tax\Controllers\TaxRateController;
use App\Tax\Controllers\TaxZoneController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('/admin')->group(function () {
    Route::prefix('/dashboard')->group(function () {
        Route::view('/', 'pages.admin.dashboard.leaderboard')->name('admin.dashboard.get');
        Route::view('/media-storage', 'pages.admin.dashboard.storage_manager')->name('admin.media_storage.get');

        Route::controller(UserController::class)->group(function () {
            Route::get('/user', 'viewAdminUserListPage')->name('admin.dashboard.user.get');
            Route::get('/user/role', 'viewAdminRoleListPage')->name('admin.dashboard.user.role.get');
            Route::get('/user/permission', 'viewAdminPermissionListPage')->name('admin.dashboard.user.permission.get');

            Route::post('/user/role/{id}', 'updateRole')->name('admin.dashboard.user.role.id.post');
            Route::delete('/user/{id}', 'deleteProfile')->name('admin.dashboard.user.id.delete');
            Route::delete('/user/role/{id}', 'deleteRole')->name('admin.dashboard.user.role.delete');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('/product', 'viewAdminProductListPage')->name('admin.dashboard.product.get');
            Route::get('/product/add', 'viewAdminProductAddPage')->name('admin.dashboard.product.add.get');
            Route::get('/product/edit/{id}/', 'viewAdminProductEditPage')->name('admin.dashboard.product.edit.id.get');
            Route::post('/product', 'addProduct')->name('admin.dashboard.product.post');
            Route::post('/product/{id}', 'updateProduct')->name('admin.dashboard.product.id.post');
            Route::delete('/product/{id}', 'deleteProduct')->name('admin.dashboard.product.id.delete');
        });

        Route::controller(ProductVariantAttributeController::class)->group(function () {
            Route::get('/product/product-variant-attribute', 'viewAdminProductVariantAttributeListPage')->name('admin.dashboard.product.product-variant-attribute.get');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/category', 'viewAdminCategoryListPage')->name('admin.dashboard.category.get');
            Route::get('/category/add', 'viewAdminAddCategoryPage')->name('admin.dashboard.category.add.get');
            Route::get('/category/edit/{id}/', 'viewAdminEditCategoryPage')->name('admin.dashboard.category.edit.id.get');
            Route::post('/category', 'addCategory')->name('admin.dashboard.category.post');
            Route::post('/category/{id}', 'updateCategory')->name('admin.dashboard.category.id.post');
            Route::delete('/category/{id}', 'deleteCategory')->name('admin.dashboard.category.id.delete');
        });

        Route::controller(BrandController::class)->group(function () {
            Route::get('/brand', 'viewAdminBrandListPage')->name('admin.dashboard.brand.get');
        });

        Route::controller(ShippingClassController::class)->group(function () {
            Route::get('/shipping/shipping-class', 'viewAdminShippingClassListPage')->name('admin.dashboard.shipping.shipping-class.get');
        });

        Route::controller(ShippingZoneController::class)->group(function () {
            Route::get('/shipping/shipping-zone', 'viewAdminShippingZoneListPage')->name('admin.dashboard.shipping.shipping-zone.get');
        });

        Route::controller(ShippingMethodController::class)->group(function () {
            Route::get('/shipping/shipping-method', 'viewAdminShippingMethodListPage')->name('admin.dashboard.shipping.shipping-method.get');
        });

        Route::controller(ShippingRateController::class)->group(function () {
            Route::get('/shipping/shipping-rate', 'viewAdminShippingRateListPage')->name('admin.dashboard.shipping.shipping-rate.get');
        });

        Route::controller(TaxClassController::class)->group(function () {
            Route::get('/tax/tax-class', 'viewAdminTaxClassListPage')->name('admin.dashboard.tax.tax-class.get');
        });

        Route::controller(TaxZoneController::class)->group(function () {
            Route::get('/tax/tax-zone', 'viewAdminTaxZoneListPage')->name('admin.dashboard.tax.tax-zone.get');
        });

        Route::controller(TaxRateController::class)->group(function () {
            Route::get('/tax/tax-rate', 'viewAdminTaxRateListPage')->name('admin.dashboard.tax.tax-rate.get');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('/order', 'viewAdminOrderListPage')->name('admin.dashboard.order.get');

            Route::get('/order/{id}', 'viewAdminOrderDetailPage')->name('admin.dashboard.order.id.get');

            Route::post('/order/{id}', 'updateOrder')->name('admin.dashboard.order.id.post');

            Route::delete('/order/{id}', 'deleteAdminOrder')->name('admin.dashboard.order.id.delete');
        });

        Route::controller(PaymentController::class)->group(function () {
            Route::post('/order/{id}/pay', 'completePayment')->name('admin.dashboard.order.id.pay.post');

            Route::get('/payment/payment', 'viewAdminPaymentListPage')->name('admin.dashboard.payment.payment.get');

            Route::delete('/payment/payment/{id}', 'deleteAdminPayment')->name('admin.dashboard.payment.payment.id.delete');
        });

        Route::controller(InvoiceController::class)->group(function () {
            Route::get('/payment/invoice', 'viewAdminInvoiceListPage')->name('admin.dashboard.payment.invoice.get');

            Route::delete('/payment/invoice/{id}', 'deleteAdminInvoice')->name('admin.dashboard.payment.invoice.id.delete');
        });

        Route::controller(TransactionController::class)->group(function () {
            Route::get('/payment/transaction', 'viewAdminTransactionListPage')->name('admin.dashboard.payment.transaction.get');

            Route::delete('/payment/transaction/{id}', 'deleteAdminTransaction')->name('admin.dashboard.payment.transaction.id.delete');
        });

        Route::controller(PaymentMethodController::class)->group(function () {
            Route::get('/payment/payment-method', 'viewAdminPaymentMethodListPage')->name('admin.dashboard.payment.payment-method.get');

            Route::delete('/payment/payment-method/{id}', 'deleteAdminPaymentMethod')->name('admin.dashboard.payment.payment-method.id.delete');

            Route::post('/payment/payment-method/create-cod-method', 'createAdminCODPaymentMethod')->name('admin.dashboard.payment.payment-method.create-cod-method.post');

            Route::post('/payment/payment-method/update-cod-method/{id}', 'updateAdminCODPaymentMethod')->name('admin.dashboard.payment.payment-method.update-cod-method.id.post');

            Route::post('/payment/payment-method/create-direct-bank-method', 'createAdminDirectBankTransferPaymentMethod')->name('admin.dashboard.payment.payment-method.create-direct-bank-method.post');

            Route::post('/payment/payment-method/update-direct-bank-method/{id}', 'updateAdminDirectBankTransferPaymentMethod')->name('admin.dashboard.payment.payment-method.update-direct-bank-method.id.post');
        });
    });
});
