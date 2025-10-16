<?php

namespace App\Auth\Models;

use App\Inventory\Models\Product;
use App\Inventory\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{

    protected $table = 'wishlists';

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'moved_to_cart' => 'boolean',
            'purchased' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function jsonResponse(array $includes = []): array
    {
        $response = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'moved_to_cart' => $this->moved_to_cart,
            'purchased' => $this->purchased,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('user', $includes)) {
            $response['user'] = $this->user ? $this->user->jsonResponse() : null;
        }

        if (in_array('product', $includes)) {
            $response['product'] = $this->product ? $this->product->jsonResponse() : null;
        }

        if (in_array('productVariant', $includes)) {
            $response['product_variant'] = $this->productVariant ? $this->productVariant->jsonResponse() : null;
        }

        return $response;
    }
}
