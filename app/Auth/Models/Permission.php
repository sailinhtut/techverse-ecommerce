<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $with = [];

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function jsonResponse(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
