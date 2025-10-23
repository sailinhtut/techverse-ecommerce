<?php

namespace App\Inventory\Controllers;

use App\Auth\Models\Wishlist;
use App\Inventory\Models\Brand;
use App\Inventory\Models\Category;
use App\Inventory\Models\Product;
use App\Inventory\Models\ProductVariant;
use App\Payment\Models\PaymentMethod;
use App\Shipping\Models\ShippingClass;
use App\Tax\Models\TaxClass;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController
{
    public function showProductListUser(Request $request)
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
            $product = $product->jsonResponse(['category', 'brand', 'productVariants']);

            // dd($product);

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

    public function viewAdminProductListPage()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);

        $products->getCollection()->transform(function ($product) {
            return $product->jsonResponse(['category', 'brand', 'paymentMethods', 'shippingClass', 'taxClass']);
        });

        return view('pages.admin.dashboard.product.product_list', compact('products'));
    }

    public function viewAdminProductAddPage()
    {
        $product_categories = Category::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());;

        $product_brands = Brand::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());;

        $payment_methods = PaymentMethod::where('enabled', true)->get()->map(fn($c) => $c->jsonResponse());;

        $shipping_classes = ShippingClass::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());

        $tax_classes = TaxClass::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());

        return view('pages.admin.dashboard.product.edit_product', [
            'product_categories' => $product_categories,
            'product_brands' => $product_brands,
            'payment_methods' => $payment_methods,
            'shipping_classes' => $shipping_classes,
            'tax_classes' => $tax_classes,
        ]);
    }

    public function viewAdminProductEditPage(Request $request, string $id)
    {
        $product_categories = Category::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());;

        $product_brands = Brand::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());;

        $payment_methods = PaymentMethod::where('enabled', true)->get()->map(fn($c) => $c->jsonResponse());;

        $shipping_classes = ShippingClass::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());

        $tax_classes = TaxClass::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());

        $edit_product = Product::find($id);
        if (!$edit_product) {
            return redirect()->back()->with('error', 'Not Found Product');
        }
        $edit_product = $edit_product->jsonResponse(['category', 'brand', 'paymentMethods', 'productVariants']);

        return view('pages.admin.dashboard.product.edit_product', [
            'edit_product' => $edit_product,
            'product_categories' => $product_categories,
            'product_brands' => $product_brands,
            'payment_methods' => $payment_methods,
            'shipping_classes' => $shipping_classes,
            'tax_classes' => $tax_classes,
        ]);
    }


    public function addProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'long_description' => 'nullable|string',
            'regular_price' => 'required|numeric',
            'sku' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric',
            'enable_stock' => 'nullable|boolean',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
            'image_gallery' => 'nullable|array',
            'image_gallery.*.label' => 'required_with:image_gallery|string|max:255',
            'image_gallery.*.image' => 'required_with:image_gallery|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'exists:payment_methods,id',
            'shipping_class_id' => 'nullable|exists:shipping_classes,id',
            'tax_class_id' => 'nullable|exists:tax_classes,id',

            'product_variants' => 'nullable|array',
            'product_variants.*.sku' => 'required|string|max:255',
            'product_variants.*.regular_price' => 'required|numeric|min:0',
            'product_variants.*.sale_price' => 'nullable|numeric|min:0',
            'product_variants.*.stock' => 'required|integer|min:0',
            'product_variants.*.weight' => 'nullable|numeric|min:0',
            'product_variants.*.combination' => 'nullable|array',
            'product_variants.*.image' => 'nullable|image|max:2048',
            'product_variants.*.remove_image' => 'nullable|boolean',
        ], [
            'product_variants.array' => 'The product variants must be a valid array.',
            'product_variants.*.sku.required' => 'Each product variant must have a SKU.',
            'product_variants.*.sku.string' => 'The SKU must be a valid string.',
            'product_variants.*.sku.max' => 'The SKU cannot exceed 255 characters.',
            'product_variants.*.regular_price.required' => 'Each variant must have a regular price.',
            'product_variants.*.regular_price.numeric' => 'The regular price must be a valid number.',
            'product_variants.*.regular_price.min' => 'The regular price cannot be negative.',
            'product_variants.*.sale_price.numeric' => 'The sale price must be a valid number.',
            'product_variants.*.sale_price.min' => 'The sale price cannot be negative.',
            'product_variants.*.stock.required' => 'Each variant must have a stock quantity.',
            'product_variants.*.stock.integer' => 'The stock quantity must be a whole number.',
            'product_variants.*.stock.min' => 'The stock quantity cannot be negative.',
            'product_variants.*.weight.numeric' => 'The weight must be a valid number.',
            'product_variants.*.weight.min' => 'The weight cannot be negative.',
            'product_variants.*.combination.array' => 'The combination must be a valid array.',
            'product_variants.*.image.image' => 'The uploaded file must be an image.',
            'product_variants.*.image.max' => 'The image cannot exceed 2MB.',
            'product_variants.*.remove_image.boolean' => 'The remove_image field must be true or false.',
        ]);

        DB::beginTransaction();

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
                'brand_id' => $validated['brand_id'] ?? null,
                'shipping_class_id' => $validated['shipping_class_id'] ?? null,
                'tax_class_id' => $validated['tax_class_id'] ?? null,
            ]);

            if (!empty($validated['payment_methods'])) {
                $new_product->paymentMethods()->sync($validated['payment_methods']);
            }


            DB::commit();

            $productVariants = $validated['product_variants'] ?? [];

            foreach ($productVariants as $variantData) {
                $variant = ProductVariant::where('product_id', $new_product->id)
                    ->where('sku', $variantData['sku'])
                    ->first();

                if ($variant) {
                    $variant->regular_price = $variantData['regular_price'] ?? 0;
                    $variant->sale_price = $variantData['sale_price'] ?? 0;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->weight = $variantData['weight'] ?? 0;
                    $variant->combination = $variantData['combination'] ?? [];
                } else {
                    // Create new variant
                    $variant = new ProductVariant();
                    $variant->product_id = $new_product->id;
                    $variant->sku = $variantData['sku'] ?? null;
                    $variant->regular_price = $variantData['regular_price'] ?? 0;
                    $variant->sale_price = $variantData['sale_price'] ?? 0;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->weight = $variantData['weight'] ?? 0;
                    $variant->combination = $variantData['combination'] ?? [];
                }

                $variant->save();

                if (!empty($variantData['remove_image']) && $variantData['remove_image'] && $variant->image) {
                    Storage::disk('public')->delete($variant->image);
                    $variant->image = null;
                    $variant->save();
                }

                if (!empty($variantData['image'])) {
                    if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                        Storage::disk('public')->delete($variant->image);
                    }

                    $imagePath = Storage::disk('public')->putFile('products/product_variants', $variantData['image']);
                    $variant->image = $imagePath;
                    $variant->save();
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product Created Successfully',
                    'data' => $new_product
                ]);
            }

            return redirect()->back()->with('success', 'Product Created Successfully');
        } catch (Exception $error) {
            DB::rollBack();
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
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'exists:payment_methods,id',
            'shipping_class_id' => 'nullable|exists:shipping_classes,id',
            'tax_class_id' => 'nullable|exists:tax_classes,id',

            'product_variants' => 'nullable|array',
            'product_variants.*.sku' => 'required|string|max:255',
            'product_variants.*.regular_price' => 'required|numeric|min:0',
            'product_variants.*.sale_price' => 'nullable|numeric|min:0',
            'product_variants.*.stock' => 'required|integer|min:0',
            'product_variants.*.weight' => 'nullable|numeric|min:0',
            'product_variants.*.combination' => 'nullable|array',
            'product_variants.*.image' => 'nullable|image|max:2048',
            'product_variants.*.remove_image' => 'nullable|boolean',
        ], [
            'product_variants.array' => 'The product variants must be a valid array.',
            'product_variants.*.sku.required' => 'Each product variant must have a SKU.',
            'product_variants.*.sku.string' => 'The SKU must be a valid string.',
            'product_variants.*.sku.max' => 'The SKU cannot exceed 255 characters.',
            'product_variants.*.regular_price.required' => 'Each variant must have a regular price.',
            'product_variants.*.regular_price.numeric' => 'The regular price must be a valid number.',
            'product_variants.*.regular_price.min' => 'The regular price cannot be negative.',
            'product_variants.*.sale_price.numeric' => 'The sale price must be a valid number.',
            'product_variants.*.sale_price.min' => 'The sale price cannot be negative.',
            'product_variants.*.stock.required' => 'Each variant must have a stock quantity.',
            'product_variants.*.stock.integer' => 'The stock quantity must be a whole number.',
            'product_variants.*.stock.min' => 'The stock quantity cannot be negative.',
            'product_variants.*.weight.numeric' => 'The weight must be a valid number.',
            'product_variants.*.weight.min' => 'The weight cannot be negative.',
            'product_variants.*.combination.array' => 'The combination must be a valid array.',
            'product_variants.*.image.image' => 'The uploaded file must be an image.',
            'product_variants.*.image.max' => 'The image cannot exceed 2MB.',
            'product_variants.*.remove_image.boolean' => 'The remove_image field must be true or false.',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);

            if ($request->has('remove_image') && $request->boolean('remove_image')) {
                Storage::disk('public')->delete($product->image);
                $product->image = null;
            }

            if ($request->hasFile('image')) {
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
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'image_gallery' => $gallery,
                'shipping_class_id' => $validated['shipping_class_id'] ?? null,
                'tax_class_id' => $validated['tax_class_id'] ?? null,
            ]);


            $product->save();

            if (isset($validated['payment_methods'])) {
                $product->paymentMethods()->sync($validated['payment_methods']);
            } else {
                $product->paymentMethods()->sync([]);
            }

            DB::commit();

            $productVariants = $validated['product_variants'] ?? [];

            foreach ($productVariants as $variantData) {
                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('sku', $variantData['sku'])
                    ->first();

                if ($variant) {
                    $variant->regular_price = $variantData['regular_price'] ?? 0;
                    $variant->sale_price = $variantData['sale_price'] ?? 0;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->weight = $variantData['weight'] ?? 0;
                    $variant->combination = $variantData['combination'] ?? [];
                } else {
                    // Create new variant
                    $variant = new ProductVariant();
                    $variant->product_id = $product->id;
                    $variant->sku = $variantData['sku'] ?? null;
                    $variant->regular_price = $variantData['regular_price'] ?? 0;
                    $variant->sale_price = $variantData['sale_price'] ?? 0;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->weight = $variantData['weight'] ?? 0;
                    $variant->combination = $variantData['combination'] ?? [];
                }

                $variant->save();

                if (!empty($variantData['remove_image']) && $variantData['remove_image'] && $variant->image) {
                    Storage::disk('public')->delete($variant->image);
                    $variant->image = null;
                    $variant->save();
                }

                if (!empty($variantData['image'])) {
                    if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                        Storage::disk('public')->delete($variant->image);
                    }

                    $imagePath = Storage::disk('public')->putFile('products/product_variants', $variantData['image']);
                    $variant->image = $imagePath;
                    $variant->save();
                }
            }

            return redirect()->back()->with('success', 'Product updated successfully');
        } catch (\Exception $error) {
            DB::rollBack();
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function deleteProduct(Request $request, string $id)
    {
        try {
            $product = Product::with('productVariants')->findOrFail($id);

            // Delete main product image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Delete product gallery images
            $image_gallery = $product->image_gallery ?? [];
            foreach ($image_gallery as $gallery) {
                if (!empty($gallery['image'])) {
                    Storage::disk('public')->delete($gallery['image']);
                }
            }

            // Delete all variant images
            foreach ($product->productVariants as $variant) {
                if ($variant->image) {
                    Storage::disk('public')->delete($variant->image);
                }
            }

            // Delete the product (variants will be deleted if you have cascade in DB or handle manually)
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

    //     public function deleteProduct(Request $request, string $id)
    //     {
    //         try {
    //             $product = Product::findOrFail($id);
    // 
    //             if ($product->image) {
    //                 Storage::disk('public')->delete($product->image);
    //             }
    // 
    //             $image_gallery = $product->image_gallery ?? [];
    //             foreach ($image_gallery as $gallery) {
    //                 Storage::disk('public')->delete($gallery['image']);
    //             }
    // 
    //             $product->delete();
    // 
    //             if ($request->expectsJson()) {
    //                 return response()->json([
    //                     'success' => true,
    //                     'message' => 'Product Deleted Successfully'
    //                 ]);
    //             }
    // 
    //             return redirect()->back()->with('success', 'Product Deleted Successfully');
    //         } catch (\Exception $error) {
    //             return handleErrors($error, "Something Went Wrong");
    //         }
    //     }
}
