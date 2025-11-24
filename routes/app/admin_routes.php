<?php

use App\Auth\Controllers\UserController;
use App\Auth\Middlewares\AdminMiddleware;
use App\Dashboard\Controllers\DashboardController;
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
use App\Inventory\Controllers\CouponController;
use App\Review\Controllers\ProductReviewController;
use App\Review\Controllers\ProductReviewReplyController;
use App\Setting\Controllers\AppSettingController;
use App\Store\Controllers\MediaImageController;
use App\Store\Controllers\StoreBranchController;

Route::middleware(['auth', 'admin'])->prefix('/admin')->group(function () {
    Route::prefix('/dashboard')->group(function () {
        Route::view('/media-storage', 'pages.admin.dashboard.storage_manager')->name('admin.media_storage.get')->middleware('admin:manage_storage');

        Route::controller(AppSettingController::class)->group(function () {
            Route::get('/setting', 'viewAdminSettingPage')->name('admin.dashboard.setting.get');
            Route::get('/setting/api/all', 'getAppSettings')->name('admin.dashboard.setting.api.all.get');
            Route::get('/setting/api/{key}', 'getAppSetting')->name('admin.dashboard.setting.api.key.get');
            Route::post('/setting/api', 'setAppSettings')->name('admin.dashboard.setting.api.post');
            Route::delete('/setting/api/{key}', 'deleteAppSetting')->name('admin.dashboard.setting.api.key.delete');
        });

        // dashboard routes
        Route::view('/', 'pages.admin.dashboard.leaderboard')->name('admin.dashboard.get')->middleware('admin:view_dashboard');

        Route::middleware('admin:view_dashboard')->controller(DashboardController::class)->group(function () {
            Route::get('/api/sale-count', 'getTotalSaleCountAPI');
            Route::get('/api/sale-amount', 'getTotalSaleAmountAPI');
            Route::get('/api/profit-amount', 'getTotalProfitAmountAPI');

            Route::get('/api/sale-product-pie', 'getSaleProductPieAPI');
            Route::get('/api/sale-category-pie', 'getSaleCategoryPieAPI');
            Route::get('/api/sale-brand-pie', 'getSaleBrandPieAPI');
        });

        // user routes
        Route::controller(UserController::class)->group(function () {

            // managing user roles
            Route::middleware('admin:manage_roles')->delete('/user/role/bulk/delete-selected', 'deleteSelectedRoles')->name('admin.dashboard.user.role.bulk.delete-selected');
            Route::middleware('admin:manage_roles')->delete('/user/role/bulk/delete-all', 'deleteAllRoles')->name('admin.dashboard.user.role.bulk.delete-all');

            Route::middleware('admin:manage_roles')->get('/user/role', 'viewAdminRoleListPage')->name('admin.dashboard.user.role.get');
            Route::middleware('admin:manage_roles')->post('/user/role', 'createRole')
                ->name('admin.dashboard.user.role.post');
            Route::middleware('admin:manage_roles')->post('/user/role/{id}', 'updateRole')
                ->name('admin.dashboard.user.role.id.post');
            Route::middleware('admin:manage_roles')->delete('/user/role/{id}', 'deleteRole')
                ->name('admin.dashboard.user.role.id.delete');

            // managing permissions
            Route::middleware('admin:manage_permissions')->get('/user/permission', 'viewAdminPermissionListPage')->name('admin.dashboard.user.permission.get');

            // managing users 
            Route::middleware('admin:manage_users')->delete('/user/bulk/delete-selected', 'deleteSelectedUsers')->name('admin.dashboard.user.bulk.delete-selected');
            Route::middleware('admin:manage_users')->delete('/user/bulk/delete-all', 'deleteAllUsers')->name('admin.dashboard.user.bulk.delete-all');

            Route::middleware('admin:manage_users')->get('/user', 'viewAdminUserListPage')->name('admin.dashboard.user.get');

            Route::middleware('admin:manage_users')->post('/user/create', 'createUserAdmin')->name('admin.dashboard.user.create.post');
            Route::middleware('admin:manage_users')->post('/user/{id}', 'updateUserAdmin')->name('admin.dashboard.user.id.post');
            Route::middleware('admin:manage_users')->delete('/user/{id}', 'deleteUserAdmin')->name('admin.dashboard.user.id.delete');
        });

        // store routes
        Route::middleware('admin:manage_media_images')->controller(MediaImageController::class)->group(function () {
            Route::delete('/store/media-image/bulk/delete-selected', 'deleteSelectedMediaImages')->name('admin.dashboard.store.media-image.bulk.delete-selected');
            Route::delete('/store/media-image/bulk/delete-all', 'deleteAllMediaImages')->name('admin.dashboard.store.media-image.bulk.delete-all');

            Route::get('/store/media-image', 'viewAdminMediaImageListPage')
                ->name('admin.dashboard.store.media-image.get');

            Route::post('/store/media-image', 'createMediaImage')
                ->name('admin.dashboard.store.media-image.post');

            Route::post('/store/media-image/{id}', 'updateMediaImage')
                ->name('admin.dashboard.media-image.id.post');

            Route::delete('/store/media-image/{id}', 'deleteMediaImage')
                ->name('admin.dashboard.media-image.id.delete');
        });

        Route::middleware('admin:manage_branches')->controller(StoreBranchController::class)->group(function () {
            Route::delete('/store/bulk/delete-selected', 'deleteSelectedStoreBranches')->name('admin.dashboard.store.bulk.delete-selected');
            Route::delete('/store/bulk/delete-all', 'deleteAllStoreBranches')->name('admin.dashboard.store.bulk.delete-all');

            Route::get('/store', 'viewAdminStoreBranchListPage')
                ->name('admin.dashboard.store.get');

            Route::post('/store', 'createStoreBranch')
                ->name('admin.dashboard.store.post');

            Route::post('/store/{id}', 'updateStoreBranch')
                ->name('admin.dashboard.store.id.post');

            Route::delete('/store/{id}', 'deleteStoreBranch')
                ->name('admin.dashboard.store.id.delete');
        });

        // product routes
        Route::middleware('admin:manage_reviews')->controller(ProductReviewReplyController::class)->group(function () {
            Route::post('/product/review/{review_id}/reply', 'createReviewReply')
                ->name('admin.dashboard.product.review.review_id.reply.post');

            Route::post('/product/review/{review_id}/reply/{reply_sid}', 'updateReviewReply')
                ->name('admin.dashboard.product.review.review_id.reply.reply_id.post');

            Route::delete('/product/review/{review_id}/reply/{reply_id}', 'deleteReviewReply')
                ->name('admin.dashboard.product.review.review_id.reply.reply_id.delete');
        });

        Route::middleware('admin:manage_reviews')->controller(ProductReviewController::class)->group(function () {

            Route::delete('/product/review/bulk/delete-selected', 'deleteSelectedReviews')->name('admin.dashboard.product.review.bulk.delete-selected');
            Route::delete('/product/review/bulk/delete-all', 'deleteAllReviews')->name('admin.dashboard.product.review.bulk.delete-all');

            Route::get('/product/review', 'viewAdminReviewListPage')
                ->name('admin.dashboard.product.review.get');

            Route::post('/product/review', 'createReview')
                ->name('admin.dashboard.product.review.post');

            Route::get('/product/review/{id}', 'viewAdminReviewDetailPage')
                ->name('admin.dashboard.product.review.id.get');

            Route::post('/product/review/{id}', 'updateReview')
                ->name('admin.dashboard.product.review.id.post');

            Route::delete('/product/review/{id}', 'deleteReview')
                ->name('admin.dashboard.product.review.id.delete');
        });

        Route::middleware('admin:manage_coupons')->controller(CouponController::class)->group(function () {

            Route::delete('/product/coupon/bulk/delete-selected', 'deleteSelectedCoupons')->name('admin.dashboard.product.coupon.bulk.delete-selected');
            Route::delete('/product/coupon/bulk/delete-all', 'deleteAllCoupons')->name('admin.dashboard.product.coupon.bulk.delete-all');

            Route::get('/product/coupon', 'viewAdminCouponListPage')->name('admin.dashboard.product.coupon.get');

            Route::post('/product/coupon', 'createCoupon')->name('admin.dashboard.product.coupon.post');

            Route::post('/product/coupon/{id}', 'updateCoupon')->name('admin.dashboard.product.coupon.id.post');

            Route::delete('/product/coupon/{id}', 'deleteCoupon')->name('admin.dashboard.product.coupon.id.delete');
        });

        Route::middleware('admin:manage_categories')->controller(CategoryController::class)->group(function () {
            Route::delete('/product/category/bulk/delete-selected', 'deleteSelectedCategories')->name('admin.dashboard.product.category.bulk.delete-selected');
            Route::delete('/product/category/bulk/delete-all', 'deleteAllCategories')->name('admin.dashboard.product.category.bulk.delete-all');

            Route::get('/category/search', 'couponSearchCategory')->name('admin.dashboard.category.search.get');
            Route::post('/category/search-ids', 'couponSearchCategoryByIds')->name('admin.dashboard.category.search-ids.post');

            Route::get('/product/category', 'viewAdminCategoryListPage')->name('admin.dashboard.product.category.get');
            Route::post('/product/category', 'addCategory')->name('admin.dashboard.product.category.post');
            Route::post('/product/category/{id}', 'updateCategory')->name('admin.dashboard.product.category.id.post');
            Route::delete('/product/category/{id}', 'deleteCategory')->name('admin.dashboard.product.category.id.delete');
        });

        Route::middleware('admin:manage_brands')->controller(BrandController::class)->group(function () {

            Route::delete('/product/brand/bulk/delete-selected', 'deleteSelectedBrands')->name('admin.dashboard.product.brand.bulk.delete-selected');
            Route::delete('/product/brand/bulk/delete-all', 'deleteAllBrands')->name('admin.dashboard.product.brand.bulk.delete-all');

            Route::get('/product/brand', 'viewAdminBrandListPage')->name('admin.dashboard.product.brand.get');
            Route::post('/product/brand', 'addBrand')->name('admin.dashboard.product.brand.post');
            Route::post('/product/brand/{id}', 'updateBrand')->name('admin.dashboard.product.brand.id.post');
            Route::delete('/product/brand/{id}', 'deleteBrand')->name('admin.dashboard.product.brand.id.delete');
        });

        Route::middleware('admin:manage_products')->controller(ProductVariantController::class)->group(function () {
            Route::delete('/variant/{id}', 'deleteVariant')->name('admin.dashboard.product.variant.id.delete');
        });

        Route::middleware('admin:manage_products')->controller(ProductController::class)->group(function () {
            Route::post('/product/bulk/update-payment-method-selected', 'updatePaymentMethodSelected')->name('admin.dashboard.product.bulk.update-payment-method-selected');
            Route::post('/product/bulk/update-payment-method-all', 'updatePaymentMethodAll')->name('admin.dashboard.product.bulk.update-payment-method-all');
            Route::post('/product/bulk/update-shipping-class-selected', 'updateShippingClassSelected')->name('admin.dashboard.product.bulk.update-shipping-class-selected');
            Route::post('/product/bulk/update-shipping-class-all', 'updateShippingClassAll')->name('admin.dashboard.product.bulk.update-shipping-class-all');
            Route::delete('/product/bulk/delete-selected', 'deleteSelectedProducts')->name('admin.dashboard.product.bulk.delete-selected');
            Route::delete('/product/bulk/delete-all', 'deleteAllProducts')->name('admin.dashboard.product.bulk.delete-all');

            Route::get('/product/search', 'searchProductsByAPI')->name('admin.dashboard.product.search.get');
            Route::post('/product/search-ids', 'searchByIds')->name('admin.dashboard.product.search-ids.post');

            Route::get('/product', 'viewAdminProductListPage')->name('admin.dashboard.product.get');
            Route::get('/product/add', 'viewAdminProductAddPage')->name('admin.dashboard.product.add.get');
            Route::get('/product/edit/{id}/', 'viewAdminProductEditPage')->name('admin.dashboard.product.edit.id.get');
            Route::post('/product', 'addProduct')->name('admin.dashboard.product.post');
            Route::post('/product/{id}', 'updateProduct')->name('admin.dashboard.product.id.post');
            Route::delete('/product/{id}', 'deleteProduct')->name('admin.dashboard.product.id.delete');
        });

        // order routes
        Route::middleware('admin:manage_orders')->controller(OrderController::class)->group(function () {
            Route::delete('/order/bulk/delete-selected', 'deleteSelectedOrders')->name('admin.dashboard.order.bulk.delete-selected');
            Route::delete('/order/bulk/delete-all', 'deleteAllOrders')->name('admin.dashboard.order.bulk.delete-all');

            Route::get('/order', 'viewAdminOrderListPage')->name('admin.dashboard.order.get');

            Route::get('/order/{id}', 'viewAdminOrderDetailPage')->name('admin.dashboard.order.id.get');

            Route::post('/order/{id}', 'updateOrder')->name('admin.dashboard.order.id.post');

            Route::delete('/order/{id}', 'deleteAdminOrder')->name('admin.dashboard.order.id.delete');
        });

        // shipping routes
        Route::middleware('admin:manage_shipping_classes')->controller(ShippingClassController::class)->group(function () {
            Route::delete('/shipping/shipping-class/bulk/delete-selected', 'deleteSelectedShippingClasses')->name('admin.dashboard.shipping.shipping-class.bulk.delete-selected');
            Route::delete('/shipping/shipping-class/bulk/delete-all', 'deleteAllShippingClasses')->name('admin.dashboard.shipping.shipping-class.bulk.delete-all');


            Route::get('/shipping/shipping-class', 'viewAdminShippingClassListPage')->name('admin.dashboard.shipping.shipping-class.get');

            Route::post('/shipping/shipping-class', 'createClass')->name('admin.dashboard.shipping.shipping-class.post');

            Route::post('/shipping/shipping-class/{id}', 'updateClass')->name('admin.dashboard.shipping.shipping-class.id.post');

            Route::delete('/shipping/shipping-class/{id}', 'deleteClass')->name('admin.dashboard.shipping.shipping-class.id.delete');
        });

        Route::middleware('admin:manage_shipping_zones')->controller(ShippingZoneController::class)->group(function () {
            Route::delete('/shipping/shipping-zone/bulk/delete-selected', 'deleteSelectedShippingZones')->name('admin.dashboard.shipping.shipping-zone.bulk.delete-selected');
            Route::delete('/shipping/shipping-zone/bulk/delete-all', 'deleteAllShippingZones')->name('admin.dashboard.shipping.shipping-zone.bulk.delete-all');


            Route::get('/shipping/shipping-zone', 'viewAdminShippingZoneListPage')->name('admin.dashboard.shipping.shipping-zone.get');

            Route::post('/shipping/shipping-zone', 'createZone')->name('admin.dashboard.shipping.shipping-zone.post');

            Route::post('/shipping/shipping-zone/{id}', 'updateZone')->name('admin.dashboard.shipping.shipping-zone.id.post');

            Route::delete('/shipping/shipping-zone/{id}', 'deleteZone')->name('admin.dashboard.shipping.shipping-zone.id.delete');
        });

        Route::middleware('admin:manage_shipping_methods')->controller(ShippingMethodController::class)->group(function () {
            Route::delete('/shipping/shipping-method/bulk/delete-selected', 'deleteSelectedShippingMethods')->name('admin.dashboard.shipping.shipping-method.bulk.delete-selected');
            Route::delete('/shipping/shipping-method/bulk/delete-all', 'deleteAllShippingMethods')->name('admin.dashboard.shipping.shipping-method.bulk.delete-all');

            Route::get('/shipping/shipping-method', 'viewAdminShippingMethodListPage')->name('admin.dashboard.shipping.shipping-method.get');

            Route::post('/shipping/shipping-method', 'createMethod')->name('admin.dashboard.shipping.shipping-method.post');

            Route::post('/shipping/shipping-method/{id}', 'updateMethod')->name('admin.dashboard.shipping.shipping-method.id.post');

            Route::delete('/shipping/shipping-method/{id}', 'deleteMethod')->name('admin.dashboard.shipping.shipping-method.id.delete');
        });

        Route::middleware('admin:manage_shipping_rates')->controller(ShippingRateController::class)->group(function () {

            Route::delete('/shipping/shipping-rate/bulk/delete-selected', 'deleteSelectedShippingRates')->name('admin.dashboard.shipping.shipping-rate.bulk.delete-selected');
            Route::delete('/shipping/shipping-rate/bulk/delete-all', 'deleteAllShippingRates')->name('admin.dashboard.shipping.shipping-rate.bulk.delete-all');


            Route::get('/shipping/shipping-rate', 'viewAdminShippingRateListPage')->name('admin.dashboard.shipping.shipping-rate.get');

            Route::post('/shipping/shipping-rate', 'createRate')->name('admin.dashboard.shipping.shipping-rate.post');

            Route::post('/shipping/shipping-rate/{id}', 'updateRate')->name('admin.dashboard.shipping.shipping-rate.id.post');

            Route::delete('/shipping/shipping-rate/{id}', 'deleteRate')->name('admin.dashboard.shipping.shipping-rate.id.delete');
        });

        // tax routes
        Route::middleware('admin:manage_tax_classes')->controller(TaxClassController::class)->group(function () {

            Route::delete('/tax/tax-class/bulk/delete-selected', 'deleteSelectedTaxClasses')->name('admin.dashboard.tax.tax-class.bulk.delete-selected');
            Route::delete('/tax/tax-class/bulk/delete-all', 'deleteAllTaxClasses')->name('admin.dashboard.tax.tax-class.bulk.delete-all');

            Route::get('/tax/tax-class', 'viewAdminTaxClassListPage')->name('admin.dashboard.tax.tax-class.get');

            Route::post('/tax/tax-class', 'createClass')->name('admin.dashboard.tax.tax-class.post');

            Route::post('/tax/tax-class/{id}', 'updateClass')->name('admin.dashboard.tax.tax-class.id.post');

            Route::delete('/tax/tax-class/{id}', 'deleteClass')->name('admin.dashboard.tax.tax-class.id.delete');
        });

        Route::middleware('admin:manage_tax_zones')->controller(TaxZoneController::class)->group(function () {
            Route::delete('/tax/tax-zone/bulk/delete-selected', 'deleteSelectedTaxZones')->name('admin.dashboard.tax.tax-zone.bulk.delete-selected');
            Route::delete('/tax/tax-zone/bulk/delete-all', 'deleteAllTaxZones')->name('admin.dashboard.tax.tax-zone.bulk.delete-all');

            Route::get('/tax/tax-zone', 'viewAdminTaxZoneListPage')->name('admin.dashboard.tax.tax-zone.get');

            Route::post('/tax/tax-zone', 'createZone')->name('admin.dashboard.tax.tax-zone.post');

            Route::post('/tax/tax-zone/{id}', 'updateZone')->name('admin.dashboard.tax.tax-zone.id.post');

            Route::delete('/tax/tax-zone/{id}', 'deleteZone')->name('admin.dashboard.tax.tax-zone.id.delete');
        });

        Route::middleware('admin:manage_tax_rates')->controller(TaxRateController::class)->group(function () {
            Route::delete('/tax/tax-rate/bulk/delete-selected', 'deleteSelectedTaxRates')->name('admin.dashboard.tax.tax-rate.bulk.delete-selected');
            Route::delete('/tax/tax-rate/bulk/delete-all', 'deleteAllTaxRates')->name('admin.dashboard.tax.tax-rate.bulk.delete-all');

            Route::get('/tax/tax-rate', 'viewAdminTaxRateListPage')->name('admin.dashboard.tax.tax-rate.get');

            Route::post('/tax/tax-rate', 'createRate')->name('admin.dashboard.tax.tax-rate.post');

            Route::post('/tax/tax-rate/{id}', 'updateRate')->name('admin.dashboard.tax.tax-rate.id.post');

            Route::delete('/tax/tax-rate/{id}', 'deleteRate')->name('admin.dashboard.tax.tax-rate.id.delete');
        });

        // payment routes
        Route::middleware('admin:manage_invoices')->controller(InvoiceController::class)->group(function () {

            Route::delete('/payment/invoice/bulk/delete-selected', 'deleteSelectedInvoices')->name('admin.dashboard.payment.invoice.bulk.delete-selected');
            Route::delete('/payment/invoice/bulk/delete-all', 'deleteAllInvoices')->name('admin.dashboard.payment.invoice.bulk.delete-all');


            Route::get('/payment/invoice', 'viewAdminInvoiceListPage')->name('admin.dashboard.payment.invoice.get');

            Route::delete('/payment/invoice/{id}', 'deleteAdminInvoice')->name('admin.dashboard.payment.invoice.id.delete');
        });

        Route::middleware('admin:manage_payments')->controller(PaymentController::class)->group(function () {
            Route::post('/order/{id}/pay', 'completePayment')->name('admin.dashboard.order.id.pay.post');

            Route::delete('/payment/payment/bulk/delete-selected', 'deleteSelectedPayments')->name('admin.dashboard.payment.payment.bulk.delete-selected');
            Route::delete('/payment/payment/bulk/delete-all', 'deleteAllPayments')->name('admin.dashboard.payment.payment.bulk.delete-all');

            Route::get('/payment/payment', 'viewAdminPaymentListPage')->name('admin.dashboard.payment.payment.get');

            Route::delete('/payment/payment/{id}', 'deleteAdminPayment')->name('admin.dashboard.payment.payment.id.delete');
        });

        Route::middleware('admin:manage_transactions')->controller(TransactionController::class)->group(function () {
            Route::delete('/payment/transaction/bulk/delete-selected', 'deleteSelectedTransactions')->name('admin.dashboard.payment.transaction.bulk.delete-selected');
            Route::delete('/payment/transaction/bulk/delete-all', 'deleteAllTransactions')->name('admin.dashboard.payment.transaction.bulk.delete-all');

            Route::get('/payment/transaction', 'viewAdminTransactionListPage')->name('admin.dashboard.payment.transaction.get');

            Route::delete('/payment/transaction/{id}', 'deleteAdminTransaction')->name('admin.dashboard.payment.transaction.id.delete');
        });

        Route::middleware('admin:manage_payment_methods')->controller(PaymentMethodController::class)->group(function () {
            Route::delete('/payment/payment-method/bulk/delete-selected', 'deleteSelectedPaymentMethods')->name('admin.dashboard.payment.payment-method.bulk.delete-selected');
            Route::delete('/payment/payment-method/bulk/delete-all', 'deleteAllPaymentMethods')->name('admin.dashboard.payment.payment-method.bulk.delete-all');

            Route::get('/payment/payment-method', 'viewAdminPaymentMethodListPage')->name('admin.dashboard.payment.payment-method.get');

            Route::delete('/payment/payment-method/{id}', 'deleteAdminPaymentMethod')->name('admin.dashboard.payment.payment-method.id.delete');

            Route::post('/payment/payment-method/create-cod-method', 'createAdminCODPaymentMethod')->name('admin.dashboard.payment.payment-method.create-cod-method.post');

            Route::post('/payment/payment-method/update-cod-method/{id}', 'updateAdminCODPaymentMethod')->name('admin.dashboard.payment.payment-method.update-cod-method.id.post');

            Route::post('/payment/payment-method/create-direct-bank-method', 'createAdminDirectBankTransferPaymentMethod')->name('admin.dashboard.payment.payment-method.create-direct-bank-method.post');

            Route::post('/payment/payment-method/update-direct-bank-method/{id}', 'updateAdminDirectBankTransferPaymentMethod')->name('admin.dashboard.payment.payment-method.update-direct-bank-method.id.post');
        });
    });
});
