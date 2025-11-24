<?php

namespace App\Order\Models;

use App\Inventory\Models\Product;
use App\Inventory\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_products';
    protected $with = [];

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'sku',
        'name',
        'quantity',
        'unit_price',
        'discount', // to remove
        'tax', // to remove
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'product_id' => 'integer',
            'variant_id' => 'integer',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id','id');
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'sku' => $this->sku,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price ?? 0,
            'discount' => (float) $this->discount ?? 0,
            'tax' => (float) $this->tax ?? 0,
            'subtotal' => (float) $this->subtotal ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('order', $eager_list) && $this->order_id) {
            $response['order'] = $this->order->jsonResponse();
        }
        if (in_array('product', $eager_list) && $this->product_id) {
            $response['product'] = $this->product->jsonResponse(['productVariants']);
        }
        if (in_array('variant', $eager_list) && $this->variant_id) {
            $response['variant'] = $this->variant->jsonResponse();
        }

        return $response;
    }
}
