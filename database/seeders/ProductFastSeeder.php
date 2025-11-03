<?php

namespace Database\Seeders;

use App\Inventory\Models\Brand;
use App\Inventory\Models\Category;
use App\Inventory\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductFastSeeder extends Seeder
{
    public function run(int $count = 1000): void
    {
        $fruitBrand = Brand::firstOrCreate(
            ['name' => 'Royal Fruit'],
            ['description' => 'Fresh and organic fruits.']
        );

        $this->command->info('Brand Data Seeded Successfully.');

        $fruitCategory = Category::firstOrCreate(
            ['name' => 'Fruit'],
            ['description' => 'Fresh and organic fruits.', 'parent_id' => null]
        );

        $this->command->info('Category Data Seeded Successfully.');

        // Base fruit types
        $baseFruits = [
            [
                'name' => 'Red Apple',
                'short_description' => 'Crisp, juicy red apples from local farms.',
                'long_description' => 'Fresh red apples packed with nutrients and natural sweetness.',
                'regular_price' => 2.50,
                'sale_price' => 1.99,
                'stock' => 150,
                'image' => url(asset('assets/images/dummy_products/apple.jpg')),
                'tags' => ['fruit', 'apple', 'fresh'],
            ],
            [
                'name' => 'Banana',
                'short_description' => 'Sweet and ripe bananas, perfect for smoothies.',
                'long_description' => 'Organically grown bananas, rich in potassium and vitamins.',
                'regular_price' => 1.80,
                'sale_price' => 1.50,
                'stock' => 200,
                'image' => url(asset('assets/images/dummy_products/banana.jpg')),
                'tags' => ['fruit', 'banana', 'organic'],
            ],
            [
                'name' => 'Fresh Orange',
                'short_description' => 'Juicy oranges full of vitamin C.',
                'long_description' => 'Sun-grown oranges with a tangy and refreshing flavor.',
                'regular_price' => 2.20,
                'sale_price' => 1.75,
                'stock' => 180,
                'image' => url(asset('assets/images/dummy_products/orange.jpg')),
                'tags' => ['fruit', 'orange', 'vitamin-c'],
            ],
            [
                'name' => 'Durian King',
                'short_description' => 'King of fruits with rich creamy taste.',
                'long_description' => 'Fresh durian with strong aroma and smooth creamy texture, ideal for durian lovers.',
                'regular_price' => 15.00,
                'sale_price' => 12.50,
                'stock' => 50,
                'image' => url(asset('assets/images/dummy_products/durian.jpg')),
                'tags' => ['fruit', 'durian', 'exotic'],
            ],
            [
                'name' => 'Golden Pineapple',
                'short_description' => 'Sweet and juicy tropical pineapple.',
                'long_description' => 'Freshly harvested pineapple with vibrant golden color and juicy texture, perfect for desserts and smoothies.',
                'regular_price' => 3.50,
                'sale_price' => 2.99,
                'stock' => 120,
                'image' => url(asset('assets/images/dummy_products/pineapple.jpg')),
                'tags' => ['fruit', 'pineapple', 'tropical'],
            ],
            [
                'name' => 'Dragon Fruit',
                'short_description' => 'Exotic dragon fruit with vibrant pink skin.',
                'long_description' => 'Fresh dragon fruit with crunchy seeds and mildly sweet taste, perfect for smoothies and fruit bowls.',
                'regular_price' => 4.00,
                'sale_price' => 3.50,
                'stock' => 80,
                'image' => url(asset('assets/images/dummy_products/dragonfruit.jpg')),
                'tags' => ['fruit', 'dragon-fruit', 'exotic'],
            ],
        ];

        $products = [];
        for ($i = 1; $i <= $count; $i++) {
            $fruit = $baseFruits[array_rand($baseFruits)];
            $name = $fruit['name'] . ' ' . $i;

            $products[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'sku' => 'FRU-' . strtoupper(Str::random(6)),
                'is_active' => true,
                'product_type' => 'simple',
                'short_description' => $fruit['short_description'],
                'long_description' => $fruit['long_description'],
                'regular_price' => $fruit['regular_price'],
                'sale_price' => $fruit['sale_price'],
                'enable_stock' => true,
                'stock' => $fruit['stock'],
                'image' => $fruit['image'],
                'image_gallery' => [
                    ['label' => 'Front View', 'image' => $fruit['image']],
                    ['label' => 'Side View', 'image' => $fruit['image']],
                ],
                'tags' => $fruit['tags'],
                'category_id' => $fruitCategory->id,
                'brand_id' => $fruitBrand->id,

                // New model fields
                'priority' => $i < 10 ? 1 : null,
                'is_pinned' => $i < 10,
                'is_promotion' => $i < 10,
                'promotion_end_time' => null,
                'interest' => $i < 10 ? $i + 1 : 0,
                'shipping_class_id' => null,
                'tax_class_id' => null,
                'length' => 10,
                'width' => 10,
                'height' => 10,
                'weight' => 20,
                'specifications' => [],
            ];
        }

        foreach ($products as $productData) {
            Product::create($productData);
            $this->command->info("{$productData['name']} Seeded Successfully.");
        }

        $this->command->info("{$count} Product(s) Seeded Successfully.");
    }
}
