<?php

namespace App\Core\Services;

use App\Inventory\Models\Product;

class ProductService
{
    public static function create(array $data)
    {
        return Product::create($data);
    }

    public static function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public static function delete(Product $product): bool
    {
        return $product->delete();
    }
}
