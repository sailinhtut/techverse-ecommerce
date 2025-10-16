<?php

namespace App\Shipping\Controllers;

use Exception;
use Illuminate\Http\Request;

class ShippingClassController
{
    public function viewShippingClassListPage()
    {
        try {
            return view('pages.admin.dashboard.shipping.shipping_class_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
