<?php

namespace App\Review\Models;

use App\Auth\Models\User;
use App\Inventory\Models\Product;
use App\Order\Models\Order;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'comment',
        'image',
        'is_approved',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'user_id' => 'integer',
        'order_id' => 'integer',
        'is_approved' => 'boolean',
        'rating' => 'decimal:1',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function replies()
    {
        return $this->hasMany(ProductReviewReply::class, 'review_id');
    }


    public function jsonResponse(array $with = []): array
    {
        $response = [
            'id' => $this->id,
            'rating' => (float) $this->rating,
            'comment' => $this->comment,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'image' => $this->image ? getDownloadableLink($this->image) : null,
            'is_approved' => $this->is_approved,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('user', $with) && $this->user_id) {
            // $response['user'] = $this->user->jsonResponse(['role']);
            $isAdmin = $this->user->hasRole('admin');

            $site_logo = getSiteLogoURL();
            $profile = $isAdmin ? $site_logo : getDownloadableLink($this->user->profile);
            $name = $isAdmin ? config('app.name') : $this->user->name;

            $response['user']['id'] = $this->user->id;
            $response['user']['name'] = $name;
            $response['user']['email'] = $this->user->email;
            $response['user']['profile'] = $profile;
        }

        if (in_array('user_full', $with) && $this->user_id) {
            $response['user'] = $this->user->jsonResponse(['role']);
        }

        if (in_array('product', $with) && $this->product_id) {
            $response['product'] = $this->product->jsonResponse();
        }

        if (in_array('order', $with) && $this->order_id) {
            $response['order'] = $this->order->jsonResponse();
        }

        if (in_array('replies', $with)) {
            $response['replies'] = $this->replies->map(fn($r) => $r->jsonResponse(['user']))->all();
        }

        return $response;
    }
}
