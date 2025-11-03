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
        'enable_stock',
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
            'enable_stock' => 'boolean',
            'stock' => 'integer',
            'weight' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $image = getDownloadableLink($this->image);
        $sale_price = (float) $this->sale_price;

        $response = [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'combination' => $this->combination,
            'regular_price' => (float)$this->regular_price ?? 0,
            'sale_price' => $sale_price <= 0 ? null : $sale_price,
            'enable_stock' => $this->enable_stock,
            'stock' => $this->stock,
            'image' => $image,
            'weight' => (float)$this->weight ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('product', $eager_list) && !is_null($this->product_id)) {
            $response['product'] = $this->product->jsonResponse();
        }

        return $response;
    }
}
