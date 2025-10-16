<?php

namespace Database\Seeders;

use App\Auth\Models\Address;
use App\Auth\Services\UserService;
use App\Inventory\Models\Category;
use App\Inventory\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $user_roles = Config::get('setup_data.user_roles', []);
        if (!empty($user_roles)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_roles')->truncate();
            DB::table('user_roles')->insert($user_roles);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->command->info('Role data seeded successfully.');
        } else {
            echo ("⚠️ No permission data found in config/user_roles.php");
        }

        $userPermissions = Config::get('setup_data.user_permission_types', []);
        if (!empty($userPermissions)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('permissions')->truncate();
            DB::table('permissions')->insert($userPermissions);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->command->info('Permission data seeded successfully.');
        } else {
            echo ("⚠️ No permission data found in config/user_permissions.php");
        }

        $user = UserService::createUser(
            [
                'name' => 'Sai Lin Htut',
                'email' => 'sailinhtut76062@gmail.com',
                'password' => Hash::make('asdf1234'),
                'email_verified_at' => now()
            ]
        );

        $this->command->info('User data seeded successfully.');

        $admin = UserService::createUser(
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('superadmin'),
                'role_id' => 3, // Admin Role
                'email_verified_at' => now()
            ]
        );

        $this->command->info('Admin data seeded successfully.[superadmin@gmail.com:superadmin]');

        Address::create([
            'user_id' => $user->id,
            'label' => 'Home',
            'recipient_name' => $user->name,
            'phone' => $user->phone_one ?? fake()->phoneNumber(),
            'street_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => 'Myanmar',
            'latitude' => fake()->latitude(16.5, 20.0),
            'longitude' => fake()->longitude(94.0, 98.0),
            'is_default_shipping' => false,
            'is_default_billing' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        Address::create([
            'user_id' => $user->id,
            'label' => 'Office',
            'recipient_name' => $user->name,
            'phone' => $user->phone_one ?? fake()->phoneNumber(),
            'street_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => 'Myanmar',
            'latitude' => fake()->latitude(16.5, 20.0),
            'longitude' => fake()->longitude(94.0, 98.0),
            'is_default_shipping' => true,
            'is_default_billing' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Address data seeded successfully.');


        $fruitCategory = Category::firstOrCreate(
            ['name' => 'Fruit'],
            [
                'description' => 'Fresh and organic fruits.',
                'parent_id' => null,
            ]
        );

        $this->command->info('Category data seeded successfully.');

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
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name']],
                $productData
            );
        }

        $this->command->info('Product data seeded successfully.');
    }
}
