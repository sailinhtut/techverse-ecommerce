<?php

namespace App\Inventory\Models;

use App\Payment\Models\PaymentMethod;
use App\Payment\Models\ProductPaymentMethod;
use App\Review\Models\ProductReview;
use App\Shipping\Models\ShippingClass;
use App\Tax\Models\TaxClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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
        'buying_price',
        'regular_price',
        'sale_price',
        'enable_stock',
        'stock',
        'category_id',
        'brand_id',
        'tags',
        'specifications',
        'priority',
        'is_pinned',
        'is_promotion',
        'promotion_end_time',
        'interest',
        'shipping_class_id',
        'tax_class_id',
        'length',
        'width',
        'height',
        'weight',
        'archived',
        'enable_review',
    ];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'enable_stock'     => 'boolean',
            'buying_price'     => 'decimal:2',
            'regular_price'    => 'decimal:2',
            'sale_price'       => 'decimal:2',
            'stock'            => 'integer',
            'image_gallery'    => 'array',
            'tags'             => 'array',
            'specifications'   => 'array',
            'priority'         => 'integer',
            'is_pinned'        => 'boolean',
            'is_promotion'     => 'boolean',
            'category_id'      => 'integer',
            'brand_id'         => 'integer',
            'shipping_class_id' => 'integer',
            'interest' => 'integer',
            'tax_class_id' => 'integer',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
            'promotion_end_time' => 'datetime',
            'enable_review' => 'boolean',
            'archived' => 'boolean',
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

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class);
    }

    public function shippingClass()
    {
        return $this->belongsTo(ShippingClass::class);
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

    public function crossSells()
    {
        return $this->belongsToMany(
            Product::class,
            'product_cross_sell',
            'product_id',
            'cross_sell_id'
        );
    }

    public function crossSellsOf()
    {
        return $this->belongsToMany(
            Product::class,
            'product_cross_sell',
            'cross_sell_id',
            'product_id'
        );
    }

    public function upSells()
    {
        return $this->belongsToMany(
            Product::class,
            'product_up_sell',
            'product_id',
            'up_sell_id'
        );
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $image = getDownloadableLink($this->image);

        $gallery = collect($this->image_gallery ?? [])->map(fn($item) => [
            'label' => $item['label'] ?? null,
            'image' => getDownloadableLink($item['image'] ?? null),
        ])->all();

        $sale_price = (float) $this->sale_price;

        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'is_active' => $this->is_active,
            'product_type' => $this->product_type,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'buying_price' => (float) $this->buying_price ?? 0,
            'regular_price' =>  (float) $this->regular_price ?? 0,
            'sale_price' => $sale_price <= 0 ? null : $sale_price,
            'enable_stock' => $this->enable_stock,
            'archived' => $this->archived,
            'stock' => $this->stock,
            'image' => $image,
            'image_gallery' => $gallery,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'shipping_class_id' => $this->shipping_class_id,
            'tax_class_id' => $this->tax_class_id,
            'product_type' => $this->product_type,
            'tags' => $this->tags,
            'specifications' => $this->specifications,
            'priority' => $this->priority,
            'is_pinned' => $this->is_pinned,
            'is_promotion' => $this->is_promotion,
            'promotion_end_time' => $this->promotion_end_time,
            'interest' => $this->interest,
            'length' => $this->length ? (float)$this->length : null,
            'width' =>  $this->width ? (float)$this->width : null,
            'height' =>  $this->height ? (float)$this->height : null,
            'weight' =>  $this->weight ? (float)$this->weight : null,
            'enable_review' => $this->enable_review, 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('category', $eager_list) && $this->category_id) {
            $response['category'] = $this->category->jsonResponse();
        }

        if (in_array('shippingClass', $eager_list) && $this->shipping_class_id) {
            $response['shipping_class'] = $this->shippingClass->jsonResponse();
        }

        if (in_array('taxClass', $eager_list) && $this->tax_class_id) {
            $response['tax_class'] = $this->taxClass->jsonResponse();
        }

        if (in_array('brand', $eager_list) && $this->brand_id) {
            $response['brand'] = $this->brand->jsonResponse();
        }

        if (in_array('paymentMethods', $eager_list)) {
            $response['payment_methods'] = $this->paymentMethods->map(fn($n) => $n->jsonResponse())->all();
        }

        if (in_array('crossSells', $eager_list)) {
            $response['cross_sell_product_ids'] = $this->crossSells->map(fn($n) => $n->id)->all();
            $response['cross_sell_products'] = $this->crossSells->map(fn($n) => $n->jsonResponse())->all();
        }

        if (in_array('upSells', $eager_list)) {
            $response['up_sell_product_ids'] = $this->upSells->map(fn($n) => $n->id)->all();
            $response['up_sell_products'] = $this->upSells->map(fn($n) => $n->jsonResponse())->all();

            if (empty($response['up_sell_products'])) {
                $related = self::where('category_id', $this->category_id)
                    ->where('id', '!=', $this->id)
                    ->where('is_active', true)
                    ->take(10)
                    ->get();

                $response['up_sell_products'] = $related->map(fn($p) => $p->jsonResponse())->all();
            }
        }

        if (in_array('productVariants', $eager_list)) {
            $response['product_variants'] = $this->productVariants->map(fn($n) => $n->jsonResponse())->all();

            $variants = $this->productVariants;
            $selectors = [];

            foreach ($variants as $variant) {
                foreach (($variant->combination ?? []) as $key => $value) {
                    if (!isset($selectors[$key])) {
                        $selectors[$key] = [];
                    }
                    if (!in_array($value, $selectors[$key])) {
                        $selectors[$key][] = $value;
                    }
                }
            }

            foreach ($selectors as &$values) {
                sort($values);
            }

            $response['product_variants_selectors'] = $selectors;
        }

        if (in_array('reviews', $eager_list)) {
            $reviews = $this->reviews()->where('is_approved', true)->get();

            $response['reviews'] = $reviews->map(fn($n) => $n->jsonResponse(['user', 'replies']))->all();
        }

        if (in_array('overall_review', $eager_list)) {
            $overall = $this->reviews()
                // ->where('is_approved', true)
                ->avg('rating');

            $rounded = $overall ? round($overall * 2) / 2 : 0;

            $response['overall_review'] = (float) number_format($rounded, 1);
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

        static::saved(fn($model) => Cache::flush());
        static::deleted(fn($model) => Cache::flush());
    }
}
