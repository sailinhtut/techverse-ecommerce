<?php

namespace App\Shipping\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $table = 'shipping_methods';
    protected $with = [];

    protected $fillable = [
        'name',
        'type',
        'cost',
        'description',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'config' => 'array',
        ];
    }

    public function jsonResponse(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'cost' => $this->cost,
            'description' => $this->description,
            'config' => $this->config,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
