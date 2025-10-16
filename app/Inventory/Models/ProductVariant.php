<?php

namespace App\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $with = [];

    protected $fillable = [
        'product_id',
        'sku',
        'combination',
        'regular_price',
        'sale_price',
        'stock',
        'image',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'combination' => 'array',
            'regular_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'weight' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function jsonResponse(): array
    {
        $image = getDownloadableLink($this->image);

        $response = [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'combination' => $this->combination,
            'regular_price' => $this->regular_price,
            'sale_price' => $this->sale_price,
            'stock' => $this->stock,
            'image' => $image,
            'weight' => $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->product) {
            $response['product'] = $this->product->jsonResponse();
        }

        return $response;
    }
}
