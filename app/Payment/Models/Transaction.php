<?php

namespace App\Payment\Models;

use App\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $with = [];

    protected $fillable = [
        'payment_id',
        'user_id',
        'reference',
        'type',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'payment_id' => 'integer',
            'user_id' => 'integer',
            'amount' => 'decimal:2',
        ];
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
            'id' => $this->id,
            'payment_id' => $this->payment_id,
            'user_id' => $this->user_id,
            'reference' => $this->reference,
            'type' => $this->type,
            'amount' => (float) $this->amount ?? 0,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('payment', $eager_list) && $this->payment_id) {
            $response['payment'] = $this->payment ? $this->payment->jsonResponse() : null;
        }

        if (in_array('user', $eager_list) && $this->user_id) {
            $response['user'] = $this->user ? $this->user->jsonResponse() : null;
        }


        return $response;
    }
}
