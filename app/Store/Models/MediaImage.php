<?php

namespace App\Store\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MediaImage extends Model
{
    protected $table = 'media_images';

    protected $fillable = [
        'title',
        'type', // carousel_slider, landing_pop_up, side_banner
        'image_path',
        'link',
        'priority',
        'is_active',
        'start_at',
        'end_at'
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_active' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime'
        ];
    }

    protected static function booted()
    {
        static::saved(fn($model) => Cache::flush());
        static::deleted(fn($model) => Cache::flush());
    }

    public function scopeActiveType($query, string $type)
    {
        $now = now();
        return $query->where('type', $type)
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->orderBy('priority', 'asc');
    }

    public function jsonResponse(): array
    {
        $image = $this->image_path ? getDownloadableLink($this->image_path) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'image' => $image,
            'link' => $this->link,
            'priority' => $this->priority,
            'is_active' => $this->is_active,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function lightJsonResponse(): array
    {
        $image = $this->image_path ? getDownloadableLink($this->image_path) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $image,
            'link' => $this->link,
            'priority' => $this->priority,
        ];
    }
}
