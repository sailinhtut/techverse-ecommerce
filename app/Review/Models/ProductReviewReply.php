<?php

namespace App\Review\Models;

use App\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;

class ProductReviewReply extends Model
{
    protected $table = 'product_review_replies';

    protected $fillable = [
        'review_id',
        'user_id',
        'reply',
    ];

    protected $casts = [
        'review_id' => 'integer',
        'user_id' => 'integer',
    ];


    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jsonResponse(array $with = []): array
    {
        $response = [
            'id' => $this->id,
            'reply' => $this->reply,
            'created_at' => $this->created_at,
            'user_id' => $this->user_id,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('user', $with) && $this->user) {
            $isFromCompany = $this->user->is_company_member;

            $profile = $isFromCompany ? asset('assets/images/master_seller_background_primary.png') : getDownloadableLink($this->user->profile);
            $name = $isFromCompany ? config('app.name') : $this->user->name;

            $response['user'] = [
                'id' => $this->user->id,
                'name' => $name,
                'email' => $this->user->email ?? null,
                'profile' => $profile,
            ];
        }

        return $response;
    }
}
