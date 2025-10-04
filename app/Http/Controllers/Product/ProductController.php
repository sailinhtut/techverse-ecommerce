<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Error;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function showProductListUser()
    {
        // $response = Http::get('https://fakestoreapi.com/products');
        // $products = $response->json();
        $products = Product::all();

        $products = $products->map(function ($product) {
            return $product->serializeJson();
        });

        return view('pages.user.core.product_list', compact('products'));
    }

    public function showProductDetail(Request $request, string $slug)
    {
        try {
            $product = Product::where('slug', $slug)->firstOrFail();
            $product = $product->serializeJson();

            return view('pages.user.core.product_detail', compact('product'));
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
            return $product->serializeJson();
        });

        return view('pages.admin.dashboard.product.product_list', compact('products'));
    }

    public function showAddProduct()
    {
        $product_categories = ProductCategory::all();
        return view('pages.admin.dashboard.product.edit_product', compact('product_categories'));
    }

    public function showEditProduct(Request $request, string $id)
    {
        $product_categories = ProductCategory::all();

        $edit_product = Product::find($id);
        if (!$edit_product) {
            return redirect()->back()->with('error', 'Not Found Product');
        }
        $edit_product = $edit_product->serializeJson();
        
        return view('pages.admin.dashboard.product.edit_product', ['edit_product' => $edit_product, 'product_categories' => $product_categories]);
    }

    public function seedProductData(Request $request)
    {
        try {
            $response = Http::get('https://fakestoreapi.com/products');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch products from FakeStore API'
                ], 500);
            }

            $categoryId = $request->query('category');
            $refresh = $request->query('refresh') ?? false;


            if (!$categoryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category ID is required'
                ], 400);
            }

            $refresh = filter_var($refresh, FILTER_VALIDATE_BOOL);
            $categoryId = intval($categoryId);

            $found_category = ProductCategory::find($categoryId);

            if (!$found_category) {
                return response()->json([
                    'success' => false,
                    'message' => "No Category Found (ID:{$categoryId})"
                ], 400);
            }


            $fakeProducts = $response->json();

            if ($refresh) {
                Product::truncate();
            }

            foreach ($fakeProducts as $item) {
                Product::create([
                    'title' => $item['title'],
                    'short_description' => substr($item['description'], 0, 255),
                    'long_description' => $item['description'],
                    'regular_price' => $item['price'],
                    'sale_price' => null,
                    'stock' => rand(1, 100),
                    'image' => $item['image'],
                    'image_gallery' => [
                        [
                            "label" => $item['title'],
                            "image" => $item['image']
                        ],
                        [
                            "label" => $item['title'],
                            "image" => $item['image']
                        ],
                        [
                            "label" => $item['title'],
                            "image" => $item['image']
                        ],
                    ],
                    'category_id' => $categoryId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Products imported successfully',
                'count'   => count($fakeProducts)
            ]);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $error->getMessage()
            ], 500);
        }
    }

    public function getProducts(Request $request)
    {
        try {
            $products = Product::all();
            return response()->json($products);
        } catch (Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function addProduct(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'long_description' => 'nullable|string',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
            'image_gallery' => 'nullable|array',
            'image_gallery.*.label' => 'required_with:image_gallery|string|max:255',
            'image_gallery.*.image' => 'required_with:image_gallery|image|max:2048',
            'category_id' => 'required|exists:product_categories,id'
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
                'title' => $validated['title'],
                'short_description' => $validated['short_description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? null,
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

    //     public function updateProduct(Request $request, string $id)
    //     {
    //         $validated = $request->validate([
    //             'title' => 'nullable|string|max:255',
    //             'short_description' => 'nullable|string|max:255',
    //             'long_description' => 'nullable|string',
    //             'regular_price' => 'nullable|numeric',
    //             'sale_price' => 'nullable|numeric',
    //             'stock' => 'nullable|integer',
    //             'image' => 'nullable|string',
    //             'image_gallery' => 'nullable|array',
    //             'image_gallery.*.label' => 'required_with:image_gallery|string|max:255',
    //             'image_gallery.*.image' => 'required_with:image_gallery|string',
    //             'category_id' => 'nullable|exists:product_categories,id'
    //         ]);
    // 
    //         try {
    //             $product = Product::findOrFail($id);
    // 
    //             $product->update([
    //                 'title' => $validated['title'] ?? $product->title,
    //                 'short_description' => $validated['short_description'] ?? $product->short_description,
    //                 'long_description' => $validated['long_description'] ?? $product->long_description,
    //                 'regular_price' => $validated['regular_price'] ?? $product->regular_price,
    //                 'sale_price' => $validated['sale_price'] ?? $product->sale_price,
    //                 'stock' => $validated['stock'] ?? $product->stock,
    //                 'image' => array_key_exists('image', $validated) ? $validated['image'] : $product->image,
    //                 'image_gallery' => array_key_exists('image_gallery', $validated) ?  $validated['image_gallery'] : $product->image_gallery,
    //                 'category_id' => $validated['category_id'] ?? $product->category_id,
    //             ]);
    // 
    //             if ($request->expectsJson()) {
    //                 return response()->json([
    //                     'success' => true,
    //                     'message' => 'Product Updated Successfully',
    //                     'data' => $product
    //                 ]);
    //             }
    // 
    //             return redirect()->back()->with('success', 'Product Updated Successfully');
    //         } catch (\Exception $error) {
    //             return handleErrors($error, "Something Went Wrong");
    //         }
    //     }

    public function updateProduct(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'long_description' => 'nullable|string',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
            'image_gallery' => 'nullable|array',
            'image_gallery.*.label' => 'required_with:image_gallery|string|max:255',
            'image_gallery.*.image' => 'nullable|image|max:2048',
            'remove_gallery' => 'nullable|array',
            'category_id' => 'required|exists:product_categories,id'
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
                'title' => $validated['title'],
                'short_description' => $validated['short_description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? null,
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
