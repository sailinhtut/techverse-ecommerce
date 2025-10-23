<?php

namespace App\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Inventory\Models\ProductAttribute;

class ProductAttributeController
{
    public function viewAdminProductAttributeListPage()
    {
        try {
            $attributes = ProductAttribute::orderBy('id', 'desc')->paginate(10);

            $attributes->getCollection()->transform(function ($attribute) {
                return $attribute->jsonResponse();
            });

            return view('pages.admin.dashboard.product.attribute_list', compact('attributes'));
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function addAttribute(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'values' => 'nullable|string|max:255'
            ]);


            $new_attribute = ProductAttribute::create([
                'name' => $validated['name'],
                'values' => $validated['values']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attribute Created Successfully',
                    'data' => $new_attribute
                ]);
            }

            return redirect()->back()->with('success', "{$validated['name']} is created successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function updateAttribute(Request $request, string $id)
    {
        try {
            $attribute = ProductAttribute::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'values' => 'nullable|string|max:255'
            ]);

            $attribute->update([
                'name' => $validated['name'],
                'values' => $validated['values']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attribute Updated Successfully',
                    'data' => $attribute
                ]);
            }

            return redirect()->back()->with('success', "{$validated['name']} is updated successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function deleteAttribute(string $id)
    {
        try {
            $attribute = ProductAttribute::findOrFail($id);
            $attribute->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attribute Deleted Successfully'
                ]);
            }

            return redirect()->back()->with('success', "{$attribute->name} is deleted successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }
}
