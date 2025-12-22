<?php

namespace App\Inventory\Models;

use App\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;

class ProductInventoryLog extends Model
{

    protected $table = 'product_inventory_logs';

    protected $fillable = [
        'product_id',
        'variant_id',
        'action_type', // in,out,adjustment
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type', // order,return,manual
        'reference_id', // record id
        'created_by',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
        ];
    }


    public function productVariant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'variant_id',
            'id'
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        return app($this->reference_type)::find($this->reference_id);
    }

    public function isStockIn(): bool
    {
        return $this->action_type === 'in';
    }

    public function isStockOut(): bool
    {
        return $this->action_type === 'out';
    }

    public function isAdjustment(): bool
    {
        return $this->action_type === 'adjustment';
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'action_type' => $this->action_type,
            'quantity' => $this->quantity,
            'stock_before' => $this->stock_before,
            'stock_after' => $this->stock_after,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('product', $eager_list)) {
            $response['product'] = $this->product
                ? $this->product->jsonResponse()
                : null;
        }

        if (in_array('productVariant', $eager_list)) {
            $response['variant'] = $this->productVariant
                ? $this->productVariant->jsonResponse()
                : ['note' => 'test'];
        }

        if (in_array('creator', $eager_list)) {
            $response['creator'] = $this->creator
                ? $this->creator->jsonResponse(['role'])
                : null;
        }

        if (in_array('reference', $eager_list)) {
            $response['reference'] = $this->reference();
        }

        return $response;
    }
}
