<?php

namespace App\Tax\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'tax_zone_id',
        'tax_class_id',
        'type',
        'is_percentage',
        'rate'
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_percentage' => 'boolean',
            'tax_zone_id' => 'integer',
            'tax_class_id' => 'integer'
        ];
    }

    public function zone()
    {
        return $this->belongsTo(TaxZone::class, 'tax_zone_id');
    }

    public function class()
    {
        return $this->belongsTo(TaxClass::class, 'tax_class_id');
    }

    public function calculateTax($item)
    {
        $item_quantity = $item['quantity'] ?? 1;
        $item_weight = $item['weight'] ?? 1;
        $item_price = $item['price'] ?? 0;

        $percent_factor = $this->rate / 100;

        $tax_cost = match ($this->type) {
            'per_item' => $this->is_percentage ? $item_price  * $percent_factor : $this->rate,
            'per_quantity' => $this->is_percentage ?  ($item_price * $percent_factor) * $item_quantity : $this->rate * $item_quantity,
            'per_weight' => $this->is_percentage ?  ($item_price * $percent_factor) * $item_weight : $this->rate * $item_weight,
            default => $this->is_percentage ? ($item_price * $item_quantity) * $percent_factor : $this->rate
        };


        return $tax_cost;
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'tax_zone_id' => $this->tax_zone_id,
            'tax_class_id' => $this->tax_class_id,

            'type' => $this->type,
            'is_percentage' => $this->is_percentage,
            'rate' => (float)$this->rate ?? 0,
        ];

        if (in_array('zone', $eager_list) && $this->zone) {
            $response['zone'] = $this->zone->jsonResponse();
        }

        if (in_array('class', $eager_list) && $this->class) {
            $response['class'] = $this->class->jsonResponse();
        }

        return $response;
    }
}
