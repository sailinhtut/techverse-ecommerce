<?php

namespace App\Shipping\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'shipping_zone_id',
        'shipping_method_id',
        'shipping_class_id',
        'type',
        'is_percentage',
        'cost'
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'is_percentage' => 'boolean',
            'shipping_zone_id' => 'integer',
            'shipping_method_id' => 'integer',
            'shipping_class_id' => 'integer'
        ];
    }

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function class()
    {
        return $this->belongsTo(ShippingClass::class, 'shipping_class_id');
    }

    public function calculateCost($item)
    {
        $quantity = $item['quantity'] ?? 1;
        $weight = $item['weight_value'] ?? 1;

        $itemPrice = $item['price'] ?? 0;
        $cost = $this->cost;

        if ($this->is_percentage) {
            $cost =  $itemPrice * $this->cost / 100;
        }

        $baseCost = match ($this->type) {
            'flat' => $cost,
            'per_item' => $cost * $quantity,
            'weight_based' => $cost * $weight,
            default => $cost,
        };


        return $baseCost;
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'shipping_zone_id' => $this->shipping_zone_id,
            'shipping_method_id' => $this->shipping_method_id,
            'shipping_class_id' => $this->shipping_class_id,

            'type' => $this->type,
            'is_percentage' => $this->is_percentage,
            'cost' => $this->cost,
        ];

        if (in_array('zone', $eager_list) && $this->zone) {
            $response['zone'] = $this->zone->jsonResponse();
        }

        if (in_array('method', $eager_list) && $this->method) {
            $response['method'] = $this->method->jsonResponse();
        }

        if (in_array('class', $eager_list) && $this->class) {
            $response['class'] = $this->class->jsonResponse();
        }

        return $response;
    }
}
