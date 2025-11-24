<?php

namespace App\Cart\Controllers;

use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use App\Inventory\Models\Product;
use App\Inventory\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class CartController
{
    public function viewUserCartPage()
    {
        try {
            // $cart = Cart::with('items.product')
            //     ->where('user_id', auth()->id())
            //     ->where('is_checked_out', false)
            //     ->first();

            return view('pages.user.core.cart');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function getCartItems()
    {
        try {
            $cart = Cart::with('items.product')
                ->where('user_id', auth()->id())
                ->where('is_checked_out', false)
                ->first();

            if (request()->expectsJson() && request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'cart' => $cart ? $cart->lightJsonResponse(['items']) : [],
                    ],
                ]);
            }
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function addToCart(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'variant_id' => 'nullable|integer|exists:product_variants,id',
                'variant_combination' => 'nullable|array',
                'quantity' => 'required|integer|min:1',
            ]);

            if (!auth()->check()) abort(401, 'Please Log In First');

            $user = Auth::user();
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'is_checked_out' => false],
                ['session_id' => session()->getId(), 'total' => 0]
            );

            $product = Product::findOrFail($validated['product_id']);
            $variant = $validated['variant_id']
                ? ProductVariant::findOrFail($validated['variant_id'])
                : null;

            if (!$validated['variant_id'] && $product->product_type == 'variable') {
                $variant =  $product->productVariants()->first();
            }

            $price = $variant && $product->product_type == 'variable'
                ? (($variant->sale_price > 0) ? $variant->sale_price : $variant->regular_price)
                : (($product->sale_price > 0) ? $product->sale_price : $product->regular_price);

            $existingItem = $cart->items()
                ->where('product_id', $product->id)
                ->where('variant_id', $variant?->id)
                ->first();

            $existingQuantity = $existingItem?->quantity ?? 0;
            $requestedQuantity = (int) $validated['quantity'];
            $totalRequested = $existingQuantity + $requestedQuantity;

            $stock = $variant && $product->product_type == 'variable'
                ? ($variant->enable_stock ? $variant->stock : null)
                : ($product->enable_stock ? $product->stock : null);

            DB::beginTransaction();

            if ($stock !== null && $existingQuantity > $stock) {
                $existingItem?->update([
                    'quantity' => 1,
                    'subtotal' => $price,
                ]);
                $cart->recalculateTotal();

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Your cart item exceeded available stock and was reset to quantity 1.',
                    'data' => [
                        'cart' => $cart->lightJsonResponse(['items']),
                    ],
                ], 400);
            }

            if ($stock !== null && $totalRequested > $stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested quantity exceeds available stock.',
                    'data' => [
                        'available_stock' => (int) $stock,
                        'existing_quantity_in_cart' => (int) $existingQuantity,
                    ],
                ], 400);
            }

            if ($existingItem) {
                $existingItem->quantity = $totalRequested;
                $existingItem->price = $price;
                $existingItem->updateSubtotal();
                $item = $existingItem;
            } else {
                $item = $cart->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'variant_combination' => $validated['variant_combination'] ?? null,
                    'name' => $variant?->name ?? $product->name,
                    'slug' => $product->slug,
                    'sku' => $variant?->sku ?? $product->sku,
                    'image' => $variant?->image ?? $product->image,
                    'price' => $price,
                    'quantity' => $requestedQuantity,
                    'subtotal' => $price * $requestedQuantity,
                ]);
            }

            $cart->recalculateTotal();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully.',
                'data' => [
                    'cart' => $cart->lightJsonResponse(['items']),

                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrors($e);
        }
    }


    public function removeFromCart(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_id' => 'required|integer|exists:cart_items,id',
                'quantity' => 'nullable|integer|min:1',
            ]);

            $cart = Cart::where('user_id', auth()->id())
                ->where('is_checked_out', false)
                ->firstOrFail();

            $item = $cart->items()->findOrFail($validated['item_id']);

            if (isset($validated['quantity'])) {
                $item->quantity -= $validated['quantity'];
                if ($item->quantity <= 0) {
                    $item->delete();
                } else {
                    $item->updateSubtotal();
                }
            } else {
                $item->delete();
            }

            $cart->recalculateTotal();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully.',
                'data' => [
                    'cart' => $cart->lightJsonResponse(['items'])
                ],
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    /**
     * 🗑️ Clear Cart (AJAX)
     */
    public function clearCart(Request $request)
    {
        try {
            $cart = Cart::where('user_id', auth()->id())
                ->where('is_checked_out', false)
                ->first();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found.',
                ], 404);
            }

            $cart->items()->delete();
            $cart->update(['total' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully.',
                'data' => [
                    'cart' => [],
                ],
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
