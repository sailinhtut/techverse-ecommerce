<?php

namespace App\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Inventory\Models\Category;

class CategoryController
{
    public function viewAdminCategoryListPage(Request $request)
    {
        $sortBy = $request->get('sortBy', 'last_updated');
        $orderBy = $request->get('orderBy', 'desc');
        $perPage = $request->get('perPage', 20);
        $search = $request->get('query', null);

        $category_type = $request->get('categoryType', null);

        $query = Category::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        switch ($category_type) {
            case 'parent_only':
                $query->whereNull('parent_id');
                break;

            case 'children_only':
                $query->whereNotNull('parent_id');
                break;

            default:
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

        $product_categories = $query->paginate(PHP_INT_MAX);
        $product_categories->appends(request()->query());

        $product_categories->getCollection()->transform(function ($category) {
            return $category->jsonResponse(['children', 'parent']);
        });

        return view('pages.admin.dashboard.category.category_list', compact('product_categories'));
    }

    public function couponSearchCategory(Request $request)
    {
        $keyword = $request->input('q');

        if (!$keyword || strlen($keyword) < 2) {
            return response()->json([
                'data' => [],
                'message' => 'Enter at least 2 characters'
            ]);
        }

        $categories = Category::select('id', 'name', 'slug')
            ->where('name', 'like', "%{$keyword}%")
            ->orWhere('slug', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function couponSearchCategoryByIds(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid category IDs provided.',
                'data' => []
            ], 400);
        }

        $categories = Category::whereIn('id', $ids)
            ->select('id', 'name', 'slug')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
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

    public function deleteSelectedCategories(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No categories selected for deletion.');
            }

            $categories = Category::whereIn('id', $ids)->get();

            foreach ($categories as $category) {
                $category->delete();
            }

            return redirect()->back()->with('success', 'Selected categories deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected categories.");
        }
    }


    public function deleteAllCategories()
    {
        try {
            // Category::truncate();
            $categories = Category::all();

            foreach ($categories as $category) {
                $category->delete();
            }

            return redirect()->back()->with('success', 'All categories deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all categories.");
        }
    }
}
