<?php

namespace App\Inventory\Models;

use App\Payment\Models\PaymentMethod;
use App\Payment\Models\ProductPaymentMethod;
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

    public function paymentMethods()
    {
        return $this->belongsToMany(
            PaymentMethod::class,
            'product_payment_methods',
            'product_id',
            'payment_method_id'
        );
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('category', $eager_list) && $this->category_id) {
            $response['category'] = $this->category->jsonResponse();
        }

        if (in_array('brand', $eager_list) && $this->brand_id) {
            $response['brand'] = $this->brand->jsonResponse();
        }

        if (in_array('paymentMethods', $eager_list)) {
            $response['payment_methods'] = $this->paymentMethods->map(fn($n) => $n->jsonResponse())->all();
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
