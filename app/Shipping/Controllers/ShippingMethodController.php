<?php

namespace App\Shipping\Controllers;

use Exception;
use Illuminate\Http\Request;

class ShippingMethodController
{
    public function viewAdminShippingMethodListPage()
    {
        try {
            return view('pages.admin.dashboard.shipping.shipping_method_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
