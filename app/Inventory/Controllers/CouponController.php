<?php

namespace App\Inventory\Controllers;

use App\Inventory\Models\Coupon;
use App\Inventory\Models\Product;
use App\Inventory\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CouponController
{
    public function viewAdminCouponListPage()
    {
        try {
            $coupons = Coupon::orderBy('updated_at', 'desc')->paginate(10);

            $coupons->getCollection()->transform(function ($coupon) {
                return $coupon->jsonResponse();
            });

            return view('pages.admin.dashboard.coupon.coupon_list', [
                'coupons' => $coupons,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function applyCoupon(Request $request)
    {
        try {
            $code = $request->input('code');
            $cart = $request->input('cart', []);


            $coupon = Coupon::where('code', $code)->first();

            if (!$coupon) {
                return response()->json(['error' => 'Invalid coupon code.'], 400);
            }

            // return response()->json(['error' => 'something went wrong','data' => $coupon->jsonResponse()],400);

            if (!is_null($coupon->usage_limit) && $coupon->used >= $coupon->usage_limit) {
                return response()->json(['error' => 'This coupon has reached its usage limit.'], 400);
            }

            $now = now();
            if (
                ($coupon->valid_from && $now->lt($coupon->valid_from)) ||
                ($coupon->valid_to && $now->gt($coupon->valid_to))
            ) {
                return response()->json(['error' => 'Coupon expired or not active.'], 400);
            }

            $productIds = $coupon->product_ids ?? [];
            $categoryIds = $coupon->category_ids ?? [];

            $subtotal = collect($cart['items'])->sum(fn($item) => $item['price'] * $item['quantity']);

            if ($coupon->min_cart_value && $subtotal < $coupon->min_cart_value) {
                return response()->json(['error' => 'Cart value too low for this coupon.'], 400);
            }

            $discountBase = 0;


            $debug = null;


            switch ($coupon->apply_to) {
                case 'product':
                    $eligibleItems = collect($cart['items'])->filter(
                        fn($item) => in_array($item['product_id'], $productIds)
                    );
                    if ($eligibleItems->isEmpty()) {
                        abort(400, 'Product is not inclusive in coupon code');
                    }
                    $discountBase = $eligibleItems->sum(fn($i) => $i['price'] * $i['quantity']);
                    break;

                case 'category':
                    $eligibleItems = collect($cart['items'])->filter(
                        fn($item) => in_array($item['category_id'], $categoryIds)
                    );
                    if ($eligibleItems->isEmpty()) {
                        abort(400, 'Product category is not inclusive in coupon code');
                    }
                    $discountBase = $eligibleItems->sum(fn($i) => $i['price'] * $i['quantity']);
                    break;

                case 'cart':
                    $discountBase = $subtotal;
                    break;
            }


            // Step 6: Calculate discount
            $discount = $coupon->type === 'percentage'
                ? ($coupon->value / 100) * $discountBase
                : $coupon->value;

            // Step 7: Prevent over-discount
            $discount = min($discount, $subtotal);

            $coupon_value = $coupon->value;
            $coupon_apply_to = ucfirst($coupon->apply_to);
            $coupon_message = $coupon->type === 'percentage' ?
                "{$coupon_apply_to} {$coupon_value}% off" :
                "{$coupon_apply_to} {$coupon_value} off";

            // Step 8: Return result
            return response()->json([
                'success' => true,
                'coupon_message' => $coupon_message,
                'coupon' => [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => (float)$coupon->value,
                    'apply_to' => $coupon->apply_to,
                ],
                'subtotal' => round($subtotal, 2),
                'discount' => round($discount, 2),
                'total' => round($subtotal - $discount, 2)
            ]);
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }


    public function createCoupon(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:50|unique:coupons,code',
                'type' => 'required|in:fixed,percentage',
                'value' => 'required|numeric|min:0',
                'apply_to' => 'required|in:product,category,cart',
                'product_ids' => 'nullable|array',
                'category_ids' => 'nullable|array',
                'min_cart_value' => 'nullable|numeric|min:0',
                'valid_from' => 'nullable|date',
                'valid_to' => 'nullable|date|after_or_equal:valid_from',
                'usage_limit' => 'nullable|integer|min:1',
                'used' => 'nullable|integer',
            ]);

            if (
                isset($validated['usage_limit']) &&
                isset($validated['used']) &&
                $validated['used'] > $validated['usage_limit']
            ) {
                return abort(400, 'Used value cannot be greater than usage limit.');
            }

            $coupon = Coupon::create([
                'code' => $validated['code'],
                'type' => $validated['type'],
                'value' => $validated['value'],
                'apply_to' => $validated['apply_to'],
                'product_ids' => $validated['product_ids'] ?? null,
                'category_ids' => $validated['category_ids'] ?? null,
                'min_cart_value' => $validated['min_cart_value'] ?? null,
                'valid_from' => $validated['valid_from'] ?? null,
                'valid_to' => $validated['valid_to'] ?? null,
                'usage_limit' => $validated['usage_limit'] ?? null,
                'used' => $validated['used'] ?? 0,
            ]);

            return redirect()->back()->with('success', 'Coupon created successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateCoupon(Request $request, $id)
    {
        try {
            $coupon = Coupon::find($id);
            if (!$coupon) abort(404, 'Coupon not found.');

            $validated = $request->validate([
                'code' => 'nullable|string|max:50|unique:coupons,code,' . $coupon->id,
                'type' => 'nullable|in:fixed,percentage',
                'value' => 'nullable|numeric|min:0',
                'apply_to' => 'nullable|in:product,category,cart',
                'product_ids' => 'nullable|array',
                'category_ids' => 'nullable|array',
                'min_cart_value' => 'nullable|numeric|min:0',
                'valid_from' => 'nullable|date',
                'valid_to' => 'nullable|date|after_or_equal:valid_from',
                'usage_limit' => 'nullable|integer|min:1',
                'used' => 'nullable|integer',
            ]);

            if (
                isset($validated['usage_limit']) &&
                isset($validated['used']) &&
                $validated['used'] > $validated['usage_limit']
            ) {
                return abort(400, 'Used value cannot be greater than usage limit.');
            }

            $coupon->update([
                'code' => $validated['code'] ?? $coupon->code,
                'type' => $validated['type'] ?? $coupon->type,
                'value' => $validated['value'] ?? $coupon->value,
                'apply_to' => $validated['apply_to'] ?? $coupon->apply_to,
                'product_ids' => $validated['product_ids'] ?? $coupon->product_ids,
                'category_ids' => $validated['category_ids'] ?? $coupon->category_ids,
                'min_cart_value' => $validated['min_cart_value'] ?? $coupon->min_cart_value,
                'valid_from' => $validated['valid_from'] ?? $coupon->valid_from,
                'valid_to' => $validated['valid_to'] ?? $coupon->valid_to,
                'usage_limit' => $validated['usage_limit'] ?? $coupon->usage_limit,
                'used' => $validated['used'] ?? $coupon->used,
            ]);

            return redirect()->back()->with('success', 'Coupon updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteCoupon($id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->delete();

            return redirect()->back()->with('success', 'Coupon deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
