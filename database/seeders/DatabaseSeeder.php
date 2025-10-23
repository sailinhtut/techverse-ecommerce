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
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            PaymentSeeder::class,
            ShippingSeeder::class,
            TaxSeeder::class,
        ]);
    }
}
