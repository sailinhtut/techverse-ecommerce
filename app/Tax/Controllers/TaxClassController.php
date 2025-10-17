<?php

namespace App\Tax\Controllers;

use Exception;
use Illuminate\Http\Request;

class TaxClassController
{
    public function viewAdminTaxClassListPage()
    {
        try {
            return view('pages.admin.dashboard.tax.tax_class_list', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
