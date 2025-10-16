<?php

namespace App\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantAttribute extends Model
{
    protected $table = 'product_variant_attributes';
    protected $with = [];

    protected $fillable = [
        'product_id',
        'name',
        'values',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'values' => 'array',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function jsonResponse(): array
    {
        $response = [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'values' => $this->values,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->product) {
            $response['product'] = $this->product->jsonResponse();
        }

        return $response;
    }
}
