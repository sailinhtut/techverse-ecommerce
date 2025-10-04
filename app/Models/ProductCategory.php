<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';

    protected $fillable = [
        'title',
        'description'
    ];

    protected function casts(): array
    {
        return [];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
