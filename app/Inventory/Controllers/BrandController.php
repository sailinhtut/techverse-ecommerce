<?php

namespace App\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Inventory\Models\Brand;

class BrandController
{
    public function viewAdminBrandListPage(Request $request)
    {
        $sortBy = $request->get('sortBy', 'last_updated');
        $orderBy = $request->get('orderBy', 'desc');
        $perPage = $request->get('perPage', 20);
        $search = $request->get('query', null);

        $query = Brand::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'last_updated':
                $query->orderBy('updated_at', $orderBy)
                    ->orderBy('id', $orderBy);
                break;

            case 'last_created':
                $query->orderBy('created_at', $orderBy)->orderBy('id', $orderBy);
                break;

            default:
                $query->orderBy('updated_at', 'desc')
                    ->orderBy('id', 'desc');
        }

        $product_brands = $query->paginate($perPage);
        $product_brands->appends(request()->query());

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

    public function deleteSelectedBrands(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No brands selected for deletion.');
            }

            $brands = Brand::whereIn('id', $ids)->get();

            foreach ($brands as $brand) {
                $brand->delete();
            }

            return redirect()->back()->with('success', 'Selected brands deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected brands.");
        }
    }


    public function deleteAllBrands()
    {
        try {
            $brands = Brand::all();

            foreach ($brands as $brand) {
                $brand->delete();
            }

            return redirect()->back()->with('success', 'All brands deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all brands.");
        }
    }
}
