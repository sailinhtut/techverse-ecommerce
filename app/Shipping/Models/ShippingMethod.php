<?php

namespace App\Shipping\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = ['name', 'description', 'enabled', 'is_free'];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'is_free' => 'boolean',

        ];
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class, 'shipping_method_id');
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'is_free' => $this->is_free
        ];

        if (in_array('rates', $eager_list)) {
            $response['rates'] = $this->rates->map(fn($r) => $r->jsonResponse())->all();
        }

        return $response;
    }
}
