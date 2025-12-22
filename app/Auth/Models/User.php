<?php

namespace App\Auth\Models;

use App\Order\Models\Invoice;
use App\Order\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $with = [];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_one',
        'phone_two',
        'profile',
        'role_id',
        'date_of_birth',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(
            Invoice::class,   // Final model
            Order::class,     // Intermediate model
            'user_id',        // Foreign key on orders table
            'order_id',       // Foreign key on invoices table
            'id',             // Local key on users table
            'id'              // Local key on orders table
        );
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class);
    }

    public function hasRole(string|array $roleNames): bool
    {
        if (!$this->role) {
            return false;
        }
        $roles = is_array($roleNames) ? $roleNames : [$roleNames];

        return in_array($this->role->name, $roles, true);
    }

    public function getPermissionList()
    {
        if (!$this->role) return [];
        $permissions = $this->role->permissions;

        if (empty($permissions)) {
            return [];
        }

        return is_string($permissions)
            ? json_decode($permissions, true) ?? []
            : (array) $permissions;
    }

    public function hasPermissions(array $permissions): bool
    {
        $userPermissions = $this->getPermissionList();

        if (empty($userPermissions)) {
            return false;
        }
        return count(array_intersect($permissions, $userPermissions)) > 0;
    }


    public function jsonResponse(array $egar_list = []): array
    {
        $profile = getDownloadableLink($this->profile);

        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_one' => $this->phone_one,
            'phone_two' => $this->phone_two,
            'profile' => $profile,
            'role_id' => $this->role_id,
            'date_of_birth' => $this->date_of_birth
                ? $this->date_of_birth->format('Y-m-d')
                : null,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('role', $egar_list)) {
            $response['role'] = $this->role ? $this->role->jsonResponse() : null;
        }

        if (in_array('notifications', $egar_list)) {
            $response['notifications'] = $this->notifications->map(fn($n) => $n->jsonResponse())->all();
        }

        if (in_array('invoices', $egar_list)) {
            $response['invoices'] = $this->invoices->map(fn($n) => $n->jsonResponse())->all();
        }

        return $response;
    }
}
