<?php

use App\Auth\Controllers\UserController;
use App\Inventory\Controllers\BrandController;
use App\Inventory\Controllers\CategoryController;
use App\Inventory\Controllers\ProductAttributeController;
use App\Inventory\Controllers\ProductController;
use App\Inventory\Controllers\ProductVariantAttributeController;
use App\Inventory\Controllers\ProductVariantController;
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

        Route::controller(ProductVariantController::class)->group(function () {
            Route::delete('/variant/{id}', 'deleteVariant')->name('admin.dashboard.product.variant.id.delete');
        });

        Route::controller(ProductAttributeController::class)->group(function () {
            Route::get('/product/attribute', 'viewAdminProductAttributeListPage')->name('admin.dashboard.product.attribute.get');
            Route::post('/product/attribute', 'addAttribute')->name('admin.dashboard.product.attribute.post');
            Route::post('/product/attribute/{id}', 'updateAttribute')->name('admin.dashboard.product.attribute.id.post');
            Route::delete('/product/attribute/{id}', 'deleteAttribute')->name('admin.dashboard.product.attribute.id.delete');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/product/category', 'viewAdminCategoryListPage')->name('admin.dashboard.product.category.get');
            Route::post('/product/category', 'addCategory')->name('admin.dashboard.product.category.post');
            Route::post('/product/category/{id}', 'updateCategory')->name('admin.dashboard.product.category.id.post');
            Route::delete('/product/category/{id}', 'deleteCategory')->name('admin.dashboard.product.category.id.delete');
        });

        Route::controller(BrandController::class)->group(function () {
            Route::get('/product/brand', 'viewAdminBrandListPage')->name('admin.dashboard.product.brand.get');
            Route::post('/product/brand', 'addBrand')->name('admin.dashboard.product.brand.post');
            Route::post('/product/brand/{id}', 'updateBrand')->name('admin.dashboard.product.brand.id.post');
            Route::delete('/product/brand/{id}', 'deleteBrand')->name('admin.dashboard.product.brand.id.delete');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('/product', 'viewAdminProductListPage')->name('admin.dashboard.product.get');
            Route::get('/product/add', 'viewAdminProductAddPage')->name('admin.dashboard.product.add.get');
            Route::get('/product/edit/{id}/', 'viewAdminProductEditPage')->name('admin.dashboard.product.edit.id.get');
            Route::post('/product', 'addProduct')->name('admin.dashboard.product.post');
            Route::post('/product/{id}', 'updateProduct')->name('admin.dashboard.product.id.post');
            Route::delete('/product/{id}', 'deleteProduct')->name('admin.dashboard.product.id.delete');
        });

        Route::controller(ShippingClassController::class)->group(function () {
            Route::get('/shipping/shipping-class', 'viewAdminShippingClassListPage')->name('admin.dashboard.shipping.shipping-class.get');

            Route::post('/shipping/shipping-class', 'createClass')->name('admin.dashboard.shipping.shipping-class.post');

            Route::post('/shipping/shipping-class/{id}', 'updateClass')->name('admin.dashboard.shipping.shipping-class.id.post');

            Route::delete('/shipping/shipping-class/{id}', 'deleteClass')->name('admin.dashboard.shipping.shipping-class.id.delete');
        });

        Route::controller(ShippingZoneController::class)->group(function () {
            Route::get('/shipping/shipping-zone', 'viewAdminShippingZoneListPage')->name('admin.dashboard.shipping.shipping-zone.get');

            Route::post('/shipping/shipping-zone', 'createZone')->name('admin.dashboard.shipping.shipping-zone.post');

            Route::post('/shipping/shipping-zone/{id}', 'updateZone')->name('admin.dashboard.shipping.shipping-zone.id.post');

            Route::delete('/shipping/shipping-zone/{id}', 'deleteZone')->name('admin.dashboard.shipping.shipping-zone.id.delete');
        });

        Route::controller(ShippingMethodController::class)->group(function () {
            Route::get('/shipping/shipping-method', 'viewAdminShippingMethodListPage')->name('admin.dashboard.shipping.shipping-method.get');

            Route::post('/shipping/shipping-method', 'createMethod')->name('admin.dashboard.shipping.shipping-method.post');

            Route::post('/shipping/shipping-method/{id}', 'updateMethod')->name('admin.dashboard.shipping.shipping-method.id.post');

            Route::delete('/shipping/shipping-method/{id}', 'deleteMethod')->name('admin.dashboard.shipping.shipping-method.id.delete');
        });

        Route::controller(ShippingRateController::class)->group(function () {
            Route::get('/shipping/shipping-rate', 'viewAdminShippingRateListPage')->name('admin.dashboard.shipping.shipping-rate.get');

            Route::post('/shipping/shipping-rate', 'createRate')->name('admin.dashboard.shipping.shipping-rate.post');

            Route::post('/shipping/shipping-rate/{id}', 'updateRate')->name('admin.dashboard.shipping.shipping-rate.id.post');

            Route::delete('/shipping/shipping-rate/{id}', 'deleteRate')->name('admin.dashboard.shipping.shipping-rate.id.delete');
        });

        Route::controller(TaxClassController::class)->group(function () {
            Route::get('/tax/tax-class', 'viewAdminTaxClassListPage')->name('admin.dashboard.tax.tax-class.get');

            Route::post('/tax/tax-class', 'createClass')->name('admin.dashboard.tax.tax-class.post');

            Route::post('/tax/tax-class/{id}', 'updateClass')->name('admin.dashboard.tax.tax-class.id.post');

            Route::delete('/tax/tax-class/{id}', 'deleteClass')->name('admin.dashboard.tax.tax-class.id.delete');
        });

        Route::controller(TaxZoneController::class)->group(function () {
            Route::get('/tax/tax-zone', 'viewAdminTaxZoneListPage')->name('admin.dashboard.tax.tax-zone.get');

            Route::post('/tax/tax-zone', 'createZone')->name('admin.dashboard.tax.tax-zone.post');

            Route::post('/tax/tax-zone/{id}', 'updateZone')->name('admin.dashboard.tax.tax-zone.id.post');

            Route::delete('/tax/tax-zone/{id}', 'deleteZone')->name('admin.dashboard.tax.tax-zone.id.delete');
        });

        Route::controller(TaxRateController::class)->group(function () {
            Route::get('/tax/tax-rate', 'viewAdminTaxRateListPage')->name('admin.dashboard.tax.tax-rate.get');

            Route::post('/tax/tax-rate', 'createRate')->name('admin.dashboard.tax.tax-rate.post');

            Route::post('/tax/tax-rate/{id}', 'updateRate')->name('admin.dashboard.tax.tax-rate.id.post');

            Route::delete('/tax/tax-rate/{id}', 'deleteRate')->name('admin.dashboard.tax.tax-rate.id.delete');
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
