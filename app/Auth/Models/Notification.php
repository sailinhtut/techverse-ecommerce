<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $with = [];

    protected $fillable = [
        'user_id',
        'image',
        'title',
        'message',
        'type',
        'link',
        'priority',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jsonResponse(array $egar_list = []): array
    {
        $image = getDownloadableLink($this->image);

        $response = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'image' => $image,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'link' => $this->link,
            'priority' => $this->priority,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if (in_array('user', $egar_list)) {
            $response['user'] = $this->user ? $this->user->jsonResponse() : null;
        }
        return $response;
    }
}
