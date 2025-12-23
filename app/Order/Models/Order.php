<?php

namespace App\Order\Models;

use App\Auth\Models\User;
use App\Payment\Models\PaymentMethod;
use App\Shipping\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $with = [];

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'grand_total',
        'shipping_address',
        'billing_address',
        'shipping_method_id',
        'payment_method_id',
        'coupon_code',
        'seen_at',
        'archived',
        'stock_consumed',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'shipping_method_id' => 'integer',
            'payment_method_id' => 'integer',
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'seen_at' => 'datetime',
            'archived' => 'boolean',
            'stock_consumed' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function getProfit(): float
    {
        return $this->products->sum(function ($item) {
            $product = $item->product;

            if (!$product) {
                return 0;
            }

            $buy = (float) $product->buying_price ?? 0;
            $sell = $item->unit_price ?? 0;

            $profitPerItem = $sell - $buy;

            if ($profitPerItem < 0) {
                $profitPerItem = 0;
            }

            $profit = $profitPerItem * (int) $item->quantity;

            return $profit;
        });
    }


    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'currency' => $this->currency,
            'subtotal' => (float) $this->subtotal ?? 0,
            'discount_total' => (float) $this->discount_total ?? 0,
            'coupon_code' => $this->coupon_code,
            'tax_total' => (float) $this->tax_total ?? 0,
            'shipping_total' => (float) $this->shipping_total ?? 0,
            'grand_total' => (float) $this->grand_total ?? 0,
            'shipping_address' => $this->shipping_address,
            'billing_address' => $this->billing_address,
            'shipping_method_id' => $this->shipping_method_id,
            'payment_method_id' => $this->payment_method_id,
            'seen_at' => $this->seen_at,
            'archived' => $this->archived,
            'stock_consumed' => $this->stock_consumed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('user', $eager_list) && $this->user_id) {
            $response['user'] = $this->user->jsonResponse();
            $response['user']['role'] = $this->user->role->jsonResponse();
        }

        if (in_array('shippingMethod', $eager_list) && $this->shipping_method_id) {
            $response['shipping_method'] = $this->shippingMethod->jsonResponse();
        }

        if (in_array('paymentMethod', $eager_list) && $this->payment_method_id) {
            $response['payment_method'] = $this->paymentMethod->jsonResponse(['paymentAttributes']);
        }

        if (in_array('products', $eager_list)) {
            $response['products'] = $this->products->map(fn($p) => $p->jsonResponse(['product']))->all();
        }

        return $response;
    }
}
