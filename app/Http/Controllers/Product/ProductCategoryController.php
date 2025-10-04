<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function showCategories()
    {
        $product_categories = ProductCategory::orderBy('id', 'desc')->paginate(10);
        return view('pages.admin.dashboard.product_category.product_category_list', compact('product_categories'));
    }

    public function showAddCategory()
    {
        return view('pages.admin.dashboard.product_category.edit_product_category');
    }

    public function showEditCategory(Request $request, string $id)
    {
        $edit_category = ProductCategory::find($id);
        if (!$edit_category) {
            return redirect()->back()->with('error', 'Not Found Category');
        }
        return view('pages.admin.dashboard.product_category.edit_product_category', ['edit_category' => $edit_category]);
    }


    public function addCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:255'
            ]);

            $new_category = ProductCategory::create([
                'title' => $validated['title'],
                'description' => $validated['description']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category Created Successfully',
                    'data' => $new_category
                ]);
            }

            return redirect()->back()->with('success', "{$validated['title']} is created successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function updateCategory(Request $request, string $id)
    {
        try {
            $category = ProductCategory::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:255'
            ]);

            $category->update([
                'title' => $validated['title'],
                'description' => $validated['description']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category Updated Successfully',
                    'data' => $category
                ]);
            }
            return redirect()->back()->with('success', "{$validated['title']} is updated successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function deleteCategory(string $id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            $category->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category Deleted Successfully'
                ]);
            }

            return redirect()->back()->with('success', "{$category->title} is deleted successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }
}
