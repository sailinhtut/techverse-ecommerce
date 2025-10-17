<?php

namespace App\Inventory\Controllers;

use Exception;

class ProductVariantAttributeController
{
    public function viewAdminProductVariantAttributeListPage()
    {
        try {
            return view('pages.admin.dashboard.product.attribute_list');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
