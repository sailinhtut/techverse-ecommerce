<?php

namespace App\Cart\Models;

use App\Inventory\Models\Product;
use App\Inventory\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'variant_combination',
        'name',
        'slug',
        'sku',
        'image',
        'price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'variant_combination' => 'array',
        'price' => 'float',
        'subtotal' => 'float',
    ];

    // 🔗 Relationships
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // 🧮 Helpers
    public function updateSubtotal()
    {
        $this->subtotal = $this->price * $this->quantity;
        $this->save();
    }

    // 💬 JSON API Response
    public function jsonResponse(array $eager_list = []): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'variant_combination' => $this->variant_combination,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'image' => getDownloadableLink($this->image),
            'price' => (float) $this->price,
            'quantity' => (int) $this->quantity,
            'subtotal' => (float) $this->subtotal,
            'product' => in_array('product', $eager_list)
                ? $this->product?->jsonResponse()
                : null,
            'variant' => in_array('variant', $eager_list)
                ? $this->variant?->jsonResponse()
                : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }


    public function lightJsonResponse(array $eager_list = []): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'variant_combination' => $this->variant_combination,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'image' => getDownloadableLink($this->image),
            'price' => (float) $this->price,
            'quantity' => (int) $this->quantity,
            'subtotal' => (float) $this->subtotal,
        ];
    }
}
