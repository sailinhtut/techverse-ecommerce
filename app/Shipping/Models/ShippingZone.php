<?php

namespace App\Shipping\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'description',
        'country',
        'state',
        'city',
        'postal_code'
    ];

    protected function casts(): array
    {
        return [];
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class, 'shipping_zone_id');
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('rates', $eager_list)) {
            $response['rates'] = $this->rates->map(fn($r) => $r->jsonResponse())->all();
        }

        return $response;
    }
}
