<?php

use App\Auth\Controllers\UserController;
use App\Inventory\Controllers\CategoryController;
use App\Inventory\Controllers\ProductController;
use App\Order\Controllers\OrderController;
use App\Payment\Controllers\InvoiceController;
use App\Payment\Controllers\PaymentController;
use App\Payment\Controllers\TransactionController;
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

        Route::controller(OrderController::class)->group(function () {
            Route::get('/order', 'viewAdminOrdersPage')->name('admin.dashboard.order.get');

            Route::get('/order/shipping-setting', 'viewAdminShippingSettingPage')->name('admin.dashboard.order.shipping-setting.get');

            Route::get('/order/payment-setting', 'viewAdminPaymentSettingPage')->name('admin.dashboard.order.payment-setting.get');

            Route::get('/order/tax-setting', 'viewAdminTaxSettingPage')->name('admin.dashboard.order.tax-setting.get');

            Route::get('/order/{id}', 'viewAdminOrderDetailPage')->name('admin.dashboard.order.id.get');

            Route::post('/order/{id}', 'updateOrder')->name('admin.dashboard.order.id.post');

            Route::delete('/order/{id}', 'deleteAdminOrder')->name('admin.dashboard.order.id.delete');
        });

        Route::controller(PaymentController::class)->group(function () {
            Route::post('/order/{id}/pay', 'completePayment')->name('admin.dashboard.order.id.pay.post');

            Route::get('/payment/invoice', 'viewAdminInvoicesPage')->name('admin.dashboard.payment.invoice.get');

            Route::get('/payment/payment', 'viewAdminPaymentsPage')->name('admin.dashboard.payment.payment.get');

            Route::get('/payment/transaction', 'viewAdminTransactionsPage')->name('admin.dashboard.payment.transaction.get');

            Route::delete('/payment/payment/{id}', 'deleteAdminPayment')->name('admin.dashboard.payment.payment.id.delete');
        });

        Route::controller(InvoiceController::class)->group(function () {
            Route::delete('/payment/invoice/{id}', 'deleteAdminInvoice')->name('admin.dashboard.payment.invoice.id.delete');
        });

        Route::controller(TransactionController::class)->group(function () {
            Route::delete('/payment/transaction/{id}', 'deleteAdminTransaction')->name('admin.dashboard.payment.transaction.id.delete');
        });
    });
});
