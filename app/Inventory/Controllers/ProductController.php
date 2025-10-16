<?php

namespace App\Inventory\Controllers;

use App\Auth\Models\Wishlist;
use App\Inventory\Models\Category;
use App\Inventory\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController
{
    public function showProductListUser()
    {
        $products = Product::with('category')->orderBy('id', 'desc')->paginate(20);

        $products->getCollection()->transform(function ($product) {
            return $product->jsonResponse(['category', 'brand']);
        });

        $wishlists = [];
        if (auth()->check()) {
            $wishlists = Wishlist::where('user_id', auth()->id())->get();
            $wishlists = $wishlists->map(fn($w) => $w->jsonResponse());
        }


        return view('pages.user.core.product_list', compact('products', 'wishlists'));
    }

    public function showProductDetail(Request $request, string $slug)
    {
        try {
            $product = Product::with('category')->where('slug', $slug)->firstOrFail();
            $product = $product->jsonResponse(['category', 'brand']);

            $wishlists = [];
            if (auth()->check()) {
                $wishlists = Wishlist::where('user_id', auth()->id())->get();
                $wishlists = $wishlists->map(fn($w) => $w->jsonResponse());
            }

            return view('pages.user.core.product_detail', compact('product', 'wishlists'));
        } catch (ModelNotFoundException $error) {
            return redirect()->back()->with('status', 'Not Found Product');
        } catch (Exception $error) {
            return redirect()->back()->with('status', 'Something Went Wrong');
        }
    }

    public function showProductListAdmin()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);

        $products->getCollection()->transform(function ($product) {
            return $product->jsonResponse(['category', 'brand']);
        });

        return view('pages.admin.dashboard.product.product_list', compact('products'));
    }

    public function showAddProduct()
    {
        $product_categories = Category::all();
        return view('pages.admin.dashboard.product.edit_product', compact('product_categories'));
    }

    public function showEditProduct(Request $request, string $id)
    {
        $product_categories = Category::all();

        $edit_product = Product::find($id);
        if (!$edit_product) {
            return redirect()->back()->with('error', 'Not Found Product');
        }
        $edit_product = $edit_product->jsonResponse(['category', 'brand']);

        return view('pages.admin.dashboard.product.edit_product', ['edit_product' => $edit_product, 'product_categories' => $product_categories]);
    }


    public function addProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'long_description' => 'nullable|string',
            'regular_price' => 'required|numeric',
            'sku' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric',
            'enable_stock' => 'nullable|boolean',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
            'image_gallery' => 'nullable|array',
            'image_gallery.*.label' => 'required_with:image_gallery|string|max:255',
            'image_gallery.*.image' => 'required_with:image_gallery|image|max:2048',
            'category_id' => 'required|exists:categories,id'
        ]);

        try {

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = Storage::disk('public')->putFile('products/thumbnails', $request->file('image'));
            }

            $galleryPaths = [];
            if ($request->has('image_gallery')) {
                foreach ($request->file('image_gallery', []) as $index => $galleryItem) {
                    if (isset($galleryItem['image'])) {
                        $path = Storage::disk('public')->putFile(
                            'products/gallery',
                            $galleryItem['image']
                        );
                        $galleryPaths[] = [
                            'label' => $validated['image_gallery'][$index]['label'],
                            'image'  => $path,
                        ];
                    }
                }
            }

            $new_product = Product::create([
                'name' => $validated['name'],
                'short_description' => $validated['short_description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'enable_stock' => $validated['enable_stock'] ?? true,
                'stock' => $validated['stock'] ?? 0,
                'image' => $imagePath,
                'image_gallery' => $galleryPaths,
                'category_id' => $validated['category_id'],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product Created Successfully',
                    'data' => $new_product
                ]);
            }

            return redirect()->back()->with('success', 'Product Created Successfully');
        } catch (Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function updateProduct(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'long_description' => 'nullable|string',
            'sku' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'enable_stock' => 'nullable|boolean',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
            'image_gallery' => 'nullable|array',
            'image_gallery.*.label' => 'required_with:image_gallery|string|max:255',
            'image_gallery.*.image' => 'nullable|image|max:2048',
            'remove_gallery' => 'nullable|array',
            'category_id' => 'required|exists:categories,id'
        ]);

        try {
            $product = Product::findOrFail($id);

            if ($request->has('remove_image') && $request->boolean('remove_image')) {
                Storage::disk('public')->delete($product->image);
                $product->image = null;
            }

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $product->image = Storage::disk('public')
                    ->putFile('products/thumbnails', $request->file('image'));
            }

            $gallery = $product->image_gallery ?? [];
            if ($request->has('remove_gallery')) {
                foreach ($request->input('remove_gallery', []) as $removeKey) {
                    if (isset($gallery[$removeKey]['image'])) {
                        $oldPath = $gallery[$removeKey]['image'];
                        Storage::disk('public')->delete($oldPath);
                    }
                    unset($gallery[$removeKey]);
                }
                $gallery = array_values($gallery);
            }

            if ($request->has('image_gallery')) {
                foreach ($request->input('image_gallery') as $idx => $item) {
                    if ($request->hasFile("image_gallery.$idx.image")) {
                        $path = Storage::disk('public')
                            ->putFile('products/gallery', $request->file("image_gallery.$idx.image"));

                        $gallery[] = [
                            'label' => $item['label'],
                            'image' => $path
                        ];
                    }
                }
            }

            $product->fill([
                'name' => $validated['name'],
                'short_description' => $validated['short_description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'enable_stock' => $validated['enable_stock'] ?? true,
                'stock' => $validated['stock'] ?? null,
                'category_id' => $validated['category_id'],
                'image_gallery' => $gallery,
            ]);

            $product->save();

            return redirect()->back()->with('success', 'Product updated successfully');
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function deleteProduct(Request $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $image_gallery = $product->image_gallery ?? [];
            foreach ($image_gallery as $gallery) {
                Storage::disk('public')->delete($gallery['image']);
            }

            $product->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product Deleted Successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Product Deleted Successfully');
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }
}
