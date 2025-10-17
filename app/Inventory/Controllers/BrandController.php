<?php

namespace App\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Inventory\Models\Category;

class BrandController
{
    public function viewAdminBrandListPage()
    {
        return view('pages.admin.dashboard.brand.brand_list', []);
    }
}
