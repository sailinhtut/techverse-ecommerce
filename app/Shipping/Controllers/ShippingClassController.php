<?php

namespace App\Shipping\Controllers;

use Exception;
use Illuminate\Http\Request;

class ShippingClassController
{
    public function viewAdminShippingClassListPage()
    {
        try {
            return view('pages.admin.dashboard.shipping.shipping_class_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
