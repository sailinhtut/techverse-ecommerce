<?php

namespace App\Inventory\Controllers;

use App\Inventory\Models\Product;
use Illuminate\Http\Request;
use App\Inventory\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductVariantController
{
    public function checkVariantStock(Request $request)
    {
        $productId = $request->input('product_id');
        $selectedValues = $request->input('selected_values', []);

        $selectedValues = array_filter($selectedValues, function ($value) {
            return $value !== '' && $value !== null;
        });

        $product = Product::with('productVariants')->findOrFail($productId);

        if ($product->productVariants->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No variants available for this product.'
            ], 404);
        }

        $variant = $product->productVariants()
            ->whereJsonContains('combination', $selectedValues)
            ->first();

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->regular_price,
                'stock' => $variant->stock
            ]
        ]);
    }

    public function deleteVariant($id, Request $request)
    {
        try {
            $variant = ProductVariant::find($id);

            if (!$variant) {
                return redirect()->back()->withErrors(['error' => 'Product variant not found.']);
            }

            if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                Storage::disk('public')->delete($variant->image);
            }

            $variant->delete();

            return redirect()->back()->with('success', 'Product variant deleted successfully.');
        } catch (\Exception $e) {
            return handleErrors($e);
        }
    }
}
