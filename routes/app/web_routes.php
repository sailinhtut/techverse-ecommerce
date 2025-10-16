<?php

use App\Payment\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;





Route::controller(PaymentController::class)->group(function () {
    Route::get('/payment', 'viewUserInvoicesPage')->name('payment.get');
});
