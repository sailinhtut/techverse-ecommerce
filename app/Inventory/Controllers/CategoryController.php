<?php

namespace App\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Inventory\Models\Category;

class CategoryController
{
    public function viewAdminCategoryListPage()
    {
        $product_categories = Category::orderBy('id', 'desc')->paginate(10);

        $product_categories->getCollection()->transform(function ($category) {
            return $category->jsonResponse(['children', 'parent']);
        });

        return view('pages.admin.dashboard.category.category_list', compact('product_categories'));
    }

    public function addCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'parent_id' => 'nullable|integer|exists:categories,id'
            ]);

            $new_category = Category::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
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
                'description' => 'nullable|string|max:255',
                'parent_id' => 'nullable|integer|exists:categories,id|not_in:' . $id
            ]);

            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
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
