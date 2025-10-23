<?php

namespace App\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Inventory\Models\Brand;

class BrandController
{
    public function viewAdminBrandListPage()
    {
        $product_brands = Brand::orderBy('id', 'desc')->paginate(10);

        $product_brands->getCollection()->transform(function ($brand) {
            return $brand->jsonResponse();
        });

        return view('pages.admin.dashboard.brand.brand_list', compact('product_brands'));
    }

    public function addBrand(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255'
            ]);

            $new_brand = Brand::create([
                'name' => $validated['name'],
                'description' => $validated['description']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Brand Created Successfully',
                    'data' => $new_brand
                ]);
            }

            return redirect()->back()->with('success', "{$validated['name']} is created successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function updateBrand(Request $request, string $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255'
            ]);

            $brand->update([
                'name' => $validated['name'],
                'description' => $validated['description']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Brand Updated Successfully',
                    'data' => $brand
                ]);
            }
            return redirect()->back()->with('success', "{$validated['name']} is updated successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function deleteBrand(string $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Brand Deleted Successfully'
                ]);
            }

            return redirect()->back()->with('success', "{$brand->name} is deleted successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }
}
