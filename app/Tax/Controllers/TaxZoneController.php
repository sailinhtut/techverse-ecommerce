<?php

namespace App\Tax\Controllers;

use Exception;
use Illuminate\Http\Request;

class TaxZoneController
{
    public function viewAdminTaxZoneListPage()
    {
        try {
            return view('pages.admin.dashboard.tax.tax_zone_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
