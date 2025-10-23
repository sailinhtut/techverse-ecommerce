<?php

namespace Database\Seeders;

use App\Inventory\Models\Brand;
use App\Inventory\Models\Category;
use App\Inventory\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $fruitBrand = Brand::firstOrCreate(
            ['name' => 'Royal Fruit'],
            [
                'description' => 'Fresh and organic fruits.',
            ]
        );

        $this->command->info('Brand Data Seeded Successfully.');


        $fruitCategory = Category::firstOrCreate(
            ['name' => 'Fruit'],
            [
                'description' => 'Fresh and organic fruits.',
                'parent_id' => null,
            ]
        );

        $this->command->info('Category Data Seeded Successfully.');

        $products = [
            [
                'name' => 'Red Apple',
                'sku' => 'FRU-' . strtoupper(Str::random(6)),
                'is_active' => true,
                'product_type' => 'simple',
                'short_description' => 'Crisp, juicy red apples from local farms.',
                'long_description' => 'Fresh red apples packed with nutrients and natural sweetness.',
                'regular_price' => 2.50,
                'sale_price' => 1.99,
                'enable_stock' => true,
                'stock' => 150,
                'image' => url(asset('assets/images/dummy_products/apple.jpg')),
                'image_gallery' => [
                    ['label' => 'Front View', 'image' => url(asset('assets/images/dummy_products/apple.jpg'))],
                    ['label' => 'Side View', 'image' => url(asset('assets/images/dummy_products/apple.jpg'))],
                ],
                'tags' => ['fruit', 'apple', 'fresh'],
                'category_id' => $fruitCategory->id,
                'brand_id' => $fruitBrand->id,
            ],
            [
                'name' => 'Banana',
                'sku' => 'FRU-' . strtoupper(Str::random(6)),
                'is_active' => true,
                'product_type' => 'simple',
                'short_description' => 'Sweet and ripe bananas, perfect for smoothies.',
                'long_description' => 'Organically grown bananas, rich in potassium and vitamins.',
                'regular_price' => 1.80,
                'sale_price' => 1.50,
                'enable_stock' => true,
                'stock' => 200,
                'image' => url(asset('assets/images/dummy_products/banana.jpg')),
                'image_gallery' => [
                    ['label' => 'Bunch View', 'image' => url(asset('assets/images/dummy_products/banana.jpg'))],
                    ['label' => 'Close Up', 'image' => url(asset('assets/images/dummy_products/banana.jpg'))],
                ],
                'tags' => ['fruit', 'banana', 'organic'],
                'category_id' => $fruitCategory->id,
                'brand_id' => $fruitBrand->id,
            ],
            [
                'name' => 'Fresh Orange',
                'sku' => 'FRU-' . strtoupper(Str::random(6)),
                'is_active' => true,
                'product_type' => 'simple',
                'short_description' => 'Juicy oranges full of vitamin C.',
                'long_description' => 'Sun-grown oranges with a tangy and refreshing flavor.',
                'regular_price' => 2.20,
                'sale_price' => 1.75,
                'enable_stock' => true,
                'stock' => 180,
                'image' => url(asset('assets/images/dummy_products/orange.jpg')),
                'image_gallery' => [
                    ['label' => 'Front View', 'image' => url(asset('assets/images/dummy_products/orange.jpg'))],
                    ['label' => 'Cut View', 'image' => url(asset('assets/images/dummy_products/orange.jpg'))],
                ],
                'tags' => ['fruit', 'orange', 'vitamin-c'],
                'category_id' => $fruitCategory->id,
                'brand_id' => $fruitBrand->id,
            ],
            [
                'name' => 'Durian King',
                'sku' => 'FRU-' . strtoupper(Str::random(6)),
                'is_active' => true,
                'product_type' => 'simple',
                'short_description' => 'King of fruits with rich creamy taste.',
                'long_description' => 'Fresh durian with strong aroma and smooth creamy texture, ideal for durian lovers.',
                'regular_price' => 15.00,
                'sale_price' => 12.50,
                'enable_stock' => true,
                'stock' => 50,
                'image' => url(asset('assets/images/dummy_products/durian.jpg')),
                'image_gallery' => [
                    ['label' => 'Front View', 'image' => url(asset('assets/images/dummy_products/durian.jpg'))],
                    ['label' => 'Cut View', 'image' => url(asset('assets/images/dummy_products/durian.jpg'))],
                ],
                'tags' => ['fruit', 'durian', 'exotic'],
                'category_id' => $fruitCategory->id,
                'brand_id' => $fruitBrand->id,
            ],
            [
                'name' => 'Golden Pineapple',
                'sku' => 'FRU-' . strtoupper(Str::random(6)),
                'is_active' => true,
                'product_type' => 'simple',
                'short_description' => 'Sweet and juicy tropical pineapple.',
                'long_description' => 'Freshly harvested pineapple with vibrant golden color and juicy texture, perfect for desserts and smoothies.',
                'regular_price' => 3.50,
                'sale_price' => 2.99,
                'enable_stock' => true,
                'stock' => 120,
                'image' => url(asset('assets/images/dummy_products/pineapple.jpg')),
                'image_gallery' => [
                    ['label' => 'Front View', 'image' => url(asset('assets/images/dummy_products/pineapple.jpg'))],
                    ['label' => 'Cut View', 'image' => url(asset('assets/images/dummy_products/pineapple.jpg'))],
                ],
                'tags' => ['fruit', 'pineapple', 'tropical'],
                'category_id' => $fruitCategory->id,
                'brand_id' => $fruitBrand->id,
            ],
            [
                'name' => 'Dragon Fruit',
                'sku' => 'FRU-' . strtoupper(Str::random(6)),
                'is_active' => true,
                'product_type' => 'simple',
                'short_description' => 'Exotic dragon fruit with vibrant pink skin.',
                'long_description' => 'Fresh dragon fruit with crunchy seeds and mildly sweet taste, perfect for smoothies and fruit bowls.',
                'regular_price' => 4.00,
                'sale_price' => 3.50,
                'enable_stock' => true,
                'stock' => 80,
                'image' => url(asset('assets/images/dummy_products/dragonfruit.jpg')),
                'image_gallery' => [
                    ['label' => 'Front View', 'image' => url(asset('assets/images/dummy_products/dragonfruit.jpg'))],
                    ['label' => 'Cut View', 'image' => url(asset('assets/images/dummy_products/dragonfruit.jpg'))],
                ],
                'tags' => ['fruit', 'dragon-fruit', 'exotic'],
                'category_id' => $fruitCategory->id,
                'brand_id' => $fruitBrand->id,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name']],
                $productData
            );
        }

        $this->command->info('Product Data Seeded Successfully.');
    }
}
