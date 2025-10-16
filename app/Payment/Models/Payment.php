<?php

namespace App\Payment\Models;

use App\Inventory\Models\PaymentMethod;
use App\Payment\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $with = [];

    protected $fillable = [
        'invoice_id',
        'payment_method_id',
        'transaction_id',
        'status',
        'amount',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'invoice_id' => 'integer',
            'payment_method_id' => 'integer',
            'amount' => 'decimal:2',
            'details' => 'array',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'payment_method_id' => $this->payment_method_id,
            'transaction_id' => $this->transaction_id, // External Paymenet Gateway ID
            'status' => $this->status,
            'amount' => $this->amount,
            'details' => $this->details,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('invoice', $eager_list) && $this->invoice_id) {
            $response['invoice'] = $this->invoice ? $this->invoice->jsonResponse() : null;
        }

        if (in_array('paymentMethod', $eager_list) && $this->payment_method_id) {
            $response['paymentMethod'] = $this->paymentMethod ? $this->paymentMethod->jsonResponse() : null;
        }


        if (in_array('transactions', $eager_list)) {
            $response['transactions'] = $this->transactions->map(fn($n) => $n->jsonResponse())->all();
        }

        return $response;
    }
}
