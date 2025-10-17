<?php

namespace App\Shipping\Controllers;

use Exception;
use Illuminate\Http\Request;

class ShippingZoneController
{
    public function viewAdminShippingZoneListPage()
    {
        try {
            return view('pages.admin.dashboard.shipping.shipping_zone_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
