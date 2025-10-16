<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';
    protected $with = [];

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    public function jsonResponse(array $egar_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'permissions' => $this->permissions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('users', $egar_list)) {
            $response['users'] = $this->users->map(fn($u) => $u->jsonResponse())->all();
        }

        return $response;
    }
}
