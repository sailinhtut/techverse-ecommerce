<?php

namespace App\Payment\Controllers;

use Exception;
use Illuminate\Http\Request;

class PaymentMethodController
{
    public function viewAdminPaymentMethodListPage()
    {
        try {
            return view('pages.admin.dashboard.payment.payment_method_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
