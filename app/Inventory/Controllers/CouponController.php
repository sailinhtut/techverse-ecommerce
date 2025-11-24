<?php

namespace App\Inventory\Controllers;

use App\Cart\Models\Cart;
use App\Inventory\Models\Coupon;
use App\Inventory\Models\Product;
use App\Inventory\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CouponController
{
    public function viewAdminCouponListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = Coupon::query();

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereJsonContains('product_ids', $search)
                    ->orWhereJsonContains('category_ids', $search);
            });

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

            $coupons = $query->paginate($perPage);
            $coupons->appends(request()->query());

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

    public function checkCoupon(Request $request)
    {
        try {
            $code = $request->input('code');

            $cart = Cart::with('items.product')
                ->where('user_id', auth()->id())
                ->where('is_checked_out', false)
                ->first();

            if (!$cart || !$cart->items()->exists()) {
                return response()->json(['message' => "Your cart is empty"], 400);
            }

            $coupon = Coupon::where('code', $code)->first();

            if (!$coupon) {
                return response()->json(['error' => 'Invalid coupon code.'], 400);
            }

            $coupon_result = $coupon->calculateDiscount($cart->items);


            // Step 8: Return result
            return response()->json([
                'success' => true,
                'data' => [
                    'coupon' => [
                        'code' => $coupon->code,
                        'type' => $coupon->type,
                        'value' => (float)$coupon->value,
                        'apply_to' => $coupon->apply_to,
                    ],
                    'coupon_message' => $coupon_result['coupon_message'] ?? "",
                    'coupon_discount' => (float) $coupon_result['coupon_discount'] ?? 0,
                ],
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
                'usage_limit' => 'nullable|integer',
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
                'code' => $validated['code'],
                'type' => $validated['type'],
                'value' => $validated['value'],
                'apply_to' => $validated['apply_to'],
                'product_ids' => $validated['product_ids'] ?? [],
                'category_ids' => $validated['category_ids'] ?? [],
                'min_cart_value' => $validated['min_cart_value'],
                'valid_from' => $validated['valid_from'] ?? $coupon->valid_from,
                'valid_to' => $validated['valid_to'] ?? $coupon->valid_to,
                'usage_limit' => $validated['usage_limit'],
                'used' => $validated['used'],
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

    public function deleteSelectedCoupons(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No coupons selected for deletion.');
            }

            $coupons = Coupon::whereIn('id', $ids)->get();

            foreach ($coupons as $coupon) {
                $coupon->delete();
            }

            return redirect()->back()->with('success', 'Selected coupons deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected coupons.");
        }
    }


    public function deleteAllCoupons()
    {
        try {
            $coupons = Coupon::all();

            foreach ($coupons as $coupon) {
                $coupon->delete();
            }

            return redirect()->back()->with('success', 'All coupons deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all coupons.");
        }
    }
}
