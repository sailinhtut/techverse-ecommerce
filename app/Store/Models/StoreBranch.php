<?php

namespace App\Store\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StoreBranch extends Model
{
    protected $table = 'store_branches';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'country',
        'state',
        'city',
        'postal_code',
        'address',
        'latitude',
        'longitude',
        'open_time',
        'close_time',
        'description',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'open_time' => 'string',
            'close_time' => 'string',
            'is_active' => 'boolean',
        ];
    }


    public function jsonResponse(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'open_time' => $this->open_time,
            'close_time' => $this->close_time,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($branch) {
            if ($branch->isDirty('name')) {
                $base = Str::slug($branch->name);
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)->where('id', '!=', $branch->id)->exists()) {
                    $slug = "{$base}-" . $i++;
                }
                $branch->slug = $slug;
            }
        });

        static::saved(fn($model) => Cache::flush());
        static::deleted(fn($model) => Cache::flush());
    }
}
