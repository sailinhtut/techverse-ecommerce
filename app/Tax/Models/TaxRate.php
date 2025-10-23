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

    public function calculateTax($subtotal)
    {
        return $this->is_percentage
            ? ($subtotal * ($this->rate / 100))
            : $this->rate;
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'tax_zone_id' => $this->tax_zone_id,
            'tax_class_id' => $this->tax_class_id,

            'is_percentage' => $this->is_percentage,
            'rate' => $this->rate,
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
