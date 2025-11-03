<?php

namespace App\Payment\Models;

use App\Order\Models\Order;
use App\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $with = [];

    protected $fillable = [
        'order_id',
        'invoice_number',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'grand_total',
        'status',
        'issued_at',
        'due_at',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'issued_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'invoice_number' => $this->invoice_number,
            'subtotal' => (float) $this->subtotal ?? 0,
            'discount_total' => (float) $this->discount_total ?? 0,
            'tax_total' => (float) $this->tax_total ?? 0,
            'shipping_total' => (float) $this->shipping_total ?? 0,
            'grand_total' => (float) $this->grand_total ?? 0,
            'status' => $this->status,
            'issued_at' => $this->issued_at,
            'due_at' => $this->due_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];


        if (in_array('order', $eager_list) && $this->order_id) {
            $response['order'] = $this->order ? $this->order->jsonResponse() : null;
        }

        if (in_array('payments', $eager_list)) {
            $response['payments'] = $this->payments->map(fn($n) => $n->jsonResponse())->all();
        }


        return $response;
    }
}
