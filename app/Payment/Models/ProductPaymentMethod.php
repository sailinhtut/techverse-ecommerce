<?php

namespace App\Payment\Models;

use App\Inventory\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductPaymentMethod extends Model
{
    protected $table = 'product_payment_methods';

    protected $fillable = [
        'product_id',
        'payment_method_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'payment_method_id' => $this->payment_method_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('product', $eager_list) && $this->paymentMethod) {
            $response['product'] = $this->product->jsonResponse();
        }

        if (in_array('payment_method', $eager_list) && $this->paymentMethod) {
            $response['payment_method'] = $this->paymentMethod->jsonResponse();
        }

        return $response;
    }
}
