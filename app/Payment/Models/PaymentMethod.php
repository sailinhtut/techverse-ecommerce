<?php

namespace App\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';
    protected $with = [];

    protected $fillable = [
        'name',
        'type',
        'code',
        'enabled',
        'priority',
        'description'
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public function paymentAttributes()
    {
        return $this->hasMany(PaymentMethodAttribute::class, 'payment_method_id');
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'code' => $this->code,
            'enabled' => $this->enabled,
            'description' => $this->description,
            'priority' => $this->priority,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('paymentAttributes', $eager_list)) {
            $attributes = $this->paymentAttributes->pluck('value', 'key')->toArray();

            if ($this->code === 'direct_bank_transfer') {
                $bankAccounts = [];

                foreach ($attributes as $key => $value) {
                    if (preg_match('/bank_account_(\d+)_(\w+)/', $key, $matches)) {
                        $index = (int)$matches[1];
                        $field = $matches[2];
                        $bankAccounts[$index][$field] = $value;
                    }
                }

                ksort($bankAccounts);

                $response['payment_attributes'] = [
                    'bank_accounts' => array_values($bankAccounts)
                ];
            } else {
                $response['payment_attributes'] = $attributes;
            }
        }

        return $response;
    }
}
