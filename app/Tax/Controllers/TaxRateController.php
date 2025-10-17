<?php

namespace App\Tax\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;

class TaxRateController extends Model
{
    public function viewAdminTaxRateListPage()
    {
        try {
            return view('pages.admin.dashboard.tax.tax_rate_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
