<?php

namespace App\Inventory\Models;

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
            'usage_limit' => $this->usage_limit ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
