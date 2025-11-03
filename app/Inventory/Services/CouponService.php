<?php

namespace App\Inventory\Services;

use App\Inventory\Models\Coupon;
use Carbon\Carbon;
use Exception;

class CouponService
{
    public static function applyCoupon(string $code): bool
    {
        try {
            $coupon = Coupon::where('code', $code)->first();

            if (!$coupon) {
                abort(400, 'Coupon not found.');
            }

            $now = Carbon::now();
            if (
                ($coupon->valid_from && $now->lt($coupon->valid_from)) ||
                ($coupon->valid_to && $now->gt($coupon->valid_to))
            ) {
                abort(400, 'Coupon is not valid for this date range.');
            }

            $coupon->increment('used');

            return true;
        } catch (Exception $e) {
            throw ($e);
        }
    }
}
