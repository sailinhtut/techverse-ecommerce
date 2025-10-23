<?php

namespace App\Shipping\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingClass extends Model
{
    protected $fillable = ['name', 'description'];

    protected function casts(): array
    {
        return [];
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function jsonResponse(array $eager_list = []): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
