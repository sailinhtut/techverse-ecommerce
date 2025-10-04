<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'long_description',
        'regular_price',
        'sale_price',
        'stock',
        'image',
        'image_gallery',
        'category_id'
    ];

    protected function casts(): array
    {
        return [
            'image_gallery' => 'array',
        ];
    }

    public function serializeJson(): array
    {
        // Handle main image
        $imagePath = $this->image;
        if ($imagePath && !Str::startsWith($imagePath, ['http://', 'https://'])) {
            $imagePath = Storage::disk('public')->url($imagePath);
        }

        // Handle gallery
        $gallery = $this->image_gallery ?? [];
        $gallery = array_map(function ($item) {
            $imagePath = $item['image'] ?? null;
            if ($imagePath && !Str::startsWith($imagePath, ['http://', 'https://'])) {
                $imagePath = Storage::disk('public')->url($imagePath);
            }
            return [
                'label' => $item['label'] ?? null,
                'image' => $imagePath,
            ];
        }, $gallery);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'regular_price' => $this->regular_price,
            'sale_price' => $this->sale_price,
            'stock' => $this->stock,
            'image' => $imagePath,
            'image_gallery' => $gallery,
            'category_id' => $this->category_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = static::generateUniqueSlug($product->title);
        });

        static::updating(function ($product) {
            if ($product->isDirty('title')) {
                $product->slug = static::generateUniqueSlug($product->title);
            }
        });
    }

    public static function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
