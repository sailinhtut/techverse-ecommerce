<?php

namespace App\Order\Models;

use App\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'order_id',
        'invoice_id',
        'payment_id',
        'user_id',
        'status',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
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

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id'         => $this->id,
            'order_id'   => $this->order_id,
            'invoice_id' => $this->invoice_id,
            'payment_id' => $this->payment_id,
            'user_id'    => $this->user_id,
            'status'     => $this->status,
            'amount'     => (float) $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('order', $eager_list) && $this->order_id) {
            $response['order'] = $this->order
                ? $this->order->jsonResponse()
                : null;
        }


        if (in_array('invoice', $eager_list) && $this->invoice_id) {
            $response['invoice'] = $this->invoice
                ? $this->invoice->jsonResponse()
                : null;
        }

        if (in_array('payment', $eager_list) && $this->payment_id) {
            $response['payment'] = $this->payment
                ? $this->payment->jsonResponse()
                : null;
        }

        if (in_array('user', $eager_list) && $this->user_id) {
            $response['user'] = $this->user
                ? $this->user->jsonResponse()
                : null;
        }

        return $response;
    }
}
