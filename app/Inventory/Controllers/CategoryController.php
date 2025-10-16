<?php

namespace App\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Inventory\Models\Category;

class CategoryController
{
    public function showCategories()
    {
        $product_categories = Category::orderBy('id', 'desc')->paginate(10);

        $product_categories->getCollection()->transform(function ($category) {
            return $category->jsonResponse();
        });

        return view('pages.admin.dashboard.product_category.product_category_list', compact('product_categories'));
    }

    public function showAddCategory()
    {
        return view('pages.admin.dashboard.product_category.edit_product_category');
    }

    public function showEditCategory(Request $request, string $id)
    {
        $edit_category = Category::find($id);
        if (!$edit_category) {
            return redirect()->back()->with('error', 'Not Found Category');
        }
        return view('pages.admin.dashboard.product_category.edit_product_category', ['edit_category' => $edit_category]);
    }


    public function addCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255'
            ]);

            $new_category = Category::create([
                'name' => $validated['name'],
                'description' => $validated['description']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category Created Successfully',
                    'data' => $new_category
                ]);
            }

            return redirect()->back()->with('success', "{$validated['name']} is created successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function updateCategory(Request $request, string $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255'
            ]);

            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category Updated Successfully',
                    'data' => $category
                ]);
            }
            return redirect()->back()->with('success', "{$validated['name']} is updated successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function deleteCategory(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category Deleted Successfully'
                ]);
            }

            return redirect()->back()->with('success', "{$category->name} is deleted successfully");
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }
}
