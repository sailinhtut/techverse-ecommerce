<?php

namespace App\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodAttribute extends Model
{
    protected $table = 'payment_method_attributes';

    protected $fillable = [
        'payment_method_id',
        'key',
        'value',
    ];

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function jsonResponse(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
