<?php

namespace App\Cart\Models;

use App\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'is_checked_out',
        'total',
    ];

    protected $casts = [
        'is_checked_out' => 'boolean',
        'total' => 'float',
    ];

    // 🔗 Relationships
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🧮 Helpers
    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function recalculateTotal()
    {
        $this->total = $this->items()->sum('subtotal');
        $this->save();
    }

    // 💬 JSON API Response
    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'is_checked_out' => (bool) $this->is_checked_out,
            'total' => (float) $this->total,
            'item_count' => $this->item_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Include items if requested
        if (in_array('items', $eager_list)) {
            $response['items'] = $this->items
                ->map(fn($item) => $item->jsonResponse())
                ->all();
        }

        // Include user if requested
        if (in_array('user', $eager_list) && $this->user) {
            // Assuming your User model has a jsonResponse() helper too
            $response['user'] = $this->user->jsonResponse();
        }

        return $response;
    }

    public function lightJsonResponse(): array
    {
        $response = [
            'total' => (float) $this->total,
            'item_count' => $this->item_count,
        ];

        $response['items'] = $this->items
            ->map(fn($item) => $item->lightJsonResponse())
            ->all();

        return $response;
    }
}
