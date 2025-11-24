<?php

namespace App\Inventory\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'type',
        'value',
        'apply_to',
        'product_ids',
        'category_ids',
        'min_cart_value',
        'valid_from',
        'valid_to',
        'usage_limit',
        'used',
    ];

    protected function casts(): array
    {
        return [
            'product_ids' => 'array',
            'category_ids' => 'array',
            'min_cart_value' => 'decimal:2',
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
            'value' => 'decimal:2',
            'used' => 'integer',
        ];
    }

    public function calculateDiscount($cart_items)
    {
        if (!is_null($this->usage_limit) && $this->used >= $this->usage_limit) {
            throw new Exception("This coupon has reached its usage limit");
        }

        $now = now();
        if (
            ($this->valid_from && $now->lt($this->valid_from)) ||
            ($this->valid_to && $now->gt($this->valid_to))
        ) {
            throw new Exception("Coupon expired or not active");
        }

        $productIds = $this->product_ids ?? [];
        $categoryIds = $this->category_ids ?? [];

        $subtotal = $cart_items->sum(fn($item) => $item['price'] * $item['quantity']);

        if ($this->min_cart_value && $subtotal < $this->min_cart_value) {
            throw new Exception("Cart value too low for this coupon.");
        }

        $discountBase = 0;


        switch ($this->apply_to) {
            case 'product':
                $eligibleItems = $cart_items->filter(
                    fn($item) => in_array($item['product_id'], $productIds)
                );
                if ($eligibleItems->isEmpty()) {
                    throw new Exception("Product is not inclusive in coupon code");
                }
                $discountBase = $eligibleItems->sum(fn($i) => $i['price'] * $i['quantity']);
                break;

            case 'category':
                $eligibleItems = $cart_items->filter(
                    fn($item) => in_array($item['product']['category_id'], $categoryIds)
                );
                if ($eligibleItems->isEmpty()) {
                    throw new Exception("Product category is not inclusive in coupon code");
                }
                $discountBase = $eligibleItems->sum(fn($i) => $i['price'] * $i['quantity']);
                break;

            case 'cart':
                $discountBase = $subtotal;
                break;
        }


        // Step 6: Calculate discount
        $discount = $this->type === 'percentage'
            ? ($this->value / 100) * $discountBase
            : $this->value;

        // Step 7: Prevent over-discount
        $discount = min($discount, $subtotal);

        $coupon_value = $this->value;
        $coupon_apply_to = ucfirst($this->apply_to);
        $coupon_message = $this->type === 'percentage' ?
            "{$coupon_apply_to} {$coupon_value}% off" :
            "{$coupon_apply_to} {$coupon_value} off";
        return [
            'coupon_message' => $coupon_message,
            'coupon_discount' => $discount,
        ];
    }

    public function jsonResponse(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'value' =>  (float) $this->value,
            'apply_to' => $this->apply_to,
            'used' => $this->used,
            'product_ids' => $this->product_ids,
            'category_ids' => $this->category_ids,
            'min_cart_value' => (float) $this->min_cart_value,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'usage_limit' => $this->usage_limit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
