<?php

namespace App\Shipping\Controllers;

use Exception;
use Illuminate\Http\Request;

class ShippingRateController
{
    public function viewAdminShippingRateListPage()
    {
        try {
            return view('pages.admin.dashboard.shipping.shipping_rate_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
