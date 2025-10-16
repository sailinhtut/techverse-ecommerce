<?php

namespace App\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Product extends Model
{
    protected $table = 'products';
    protected $with = ['category', 'brand'];

    protected $fillable = [
        'name',
        'sku',
        'is_active',
        'slug',
        'image',
        'image_gallery',
        'product_type',
        'short_description',
        'long_description',
        'regular_price',
        'sale_price',
        'enable_stock',
        'stock',
        'category_id',
        'brand_id',
        'tags',
        'specifications',
        'shipping_methods',
        'tax_methods',
        'payment_methods',
    ];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'enable_stock'     => 'boolean',
            'regular_price'    => 'decimal:2',
            'sale_price'       => 'decimal:2',
            'stock'            => 'integer',
            'image_gallery'    => 'array',
            'tags'             => 'array',
            'specifications'   => 'array',
            'shipping_methods' => 'array',
            'tax_methods'      => 'array',
            'payment_methods'  => 'array',
            'category_id'      => 'integer',
            'brand_id'         => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


    public function jsonResponse(array $eager_list = []): array
    {
        $image = getDownloadableLink($this->image);

        $gallery = collect($this->image_gallery ?? [])->map(fn($item) => [
            'label' => $item['label'] ?? null,
            'image' => getDownloadableLink($item['image'] ?? null),
        ])->all();

        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'is_active' => $this->is_active,
            'product_type' => $this->product_type,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'regular_price' => $this->regular_price,
            'sale_price' => $this->sale_price,
            'enable_stock' => $this->enable_stock,
            'stock' => $this->stock,
            'image' => $image,
            'image_gallery' => $gallery,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'tags' => $this->tags,
            'specifications' => $this->specifications,
            'shipping_methods' => $this->shipping_methods,
            'tax_methods' => $this->tax_methods,
            'payment_methods' => $this->payment_methods,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('category', $eager_list) && $this->category_id) {
            $response['category'] = $this->category->jsonResponse();
        }

        if (in_array('brand', $eager_list) && $this->brand_id) {
            $response['brand'] = $this->brand->jsonResponse();
        }

        return $response;
    }


    protected static function boot()
    {
        parent::boot();
        static::saving(function ($product) {
            if ($product->isDirty('name')) {
                $base = Str::slug($product->name);
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = "{$base}-" . $i++;
                }
                $product->slug = $slug;
            }
        });
    }
}
