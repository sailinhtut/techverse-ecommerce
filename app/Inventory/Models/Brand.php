<?php

namespace App\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class Brand extends Model
{
    protected $table = 'brands';

    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('products', $eager_list)) {
            $response['products'] =  $this->products->map(fn($n) => $n->jsonResponse())->all();
        }

        return $response;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($brand) {
            if ($brand->isDirty('name')) {
                $base = Str::slug($brand->name);
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)
                    ->where('id', '!=', $brand->id)
                    ->exists()
                ) {
                    $slug = "{$base}-" . $i++;
                }
                $brand->slug = $slug;
            }
        });

        static::saved(fn($model) => Cache::flush());
        static::deleted(fn($model) => Cache::flush());
    }

   
}
