<?php

use App\Inventory\Models\Product;
use App\Inventory\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::get('/debug', function () {
    $product = Product::find(2);
    if (!$product) {
        throw new NotFoundHttpException("Product not found");
    }
    $product_json = $product->jsonResponse(['productVariants']);
    return view('_', [
        'product' => $product_json,
    ]);
});


Route::post('/debug', function (Request $request) {


    try {
        $validated = $request->validate([
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


        $productId = 2;

        $productVariants = $validated['product_variants'] ?? [];

        foreach ($productVariants as $variantData) {
            $variant = ProductVariant::where('product_id', $productId)
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
                $variant->product_id = $productId;
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

        return redirect()->back()->with('success', 'Variants Added successfully!');
    } catch (\Exception $e) {
        return handleErrors($e);
    }
});


require __DIR__ . '/app/inventory_routes.php';
require __DIR__ . '/app/auth_routes.php';
require __DIR__ . '/app/core_routes.php';
require __DIR__ . '/app/payment_routes.php';
require __DIR__ . '/app/web_routes.php';
require __DIR__ . '/app/admin_routes.php';
