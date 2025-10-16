<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';
    protected $fillable = ['email', 'token', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public $timestamps = true;

    public static function generate(string $email): self
    {
        $token = bin2hex(random_bytes(32));

        return self::create([
            'email' => $email,
            'token' => $token,
            'expires_at' => now()->addMinutes(30),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
