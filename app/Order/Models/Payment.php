<?php

namespace App\Order\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $with = [];

    protected $fillable = [
        'order_id',
        'invoice_id',
        'amount',
        'archived'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'archived' => 'boolean',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'invoice_id' => $this->invoice_id,
            'amount' => (float) $this->amount ?? 0,
            'archived' => $this->archived,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('order', $eager_list) && $this->order_id) {
            $response['order'] = $this->order ? $this->order->jsonResponse() : null;
        }

        if (in_array('invoice', $eager_list) && $this->invoice_id) {
            $response['invoice'] = $this->invoice ? $this->invoice->jsonResponse() : null;
        }

        if (in_array('transactions', $eager_list)) {
            $response['transactions'] = $this->transactions->map(fn($n) => $n->jsonResponse())->all();
        }

        return $response;
    }
}
