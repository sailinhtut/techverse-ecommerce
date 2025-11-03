<?php

namespace Database\Seeders;

use App\Shipping\Models\ShippingClass;
use App\Shipping\Models\ShippingMethod;
use App\Shipping\Models\ShippingRate;
use App\Shipping\Models\ShippingZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Standard Goods', 'description' => 'Default shipping class for general products.'],
            ['name' => 'Fragile Items', 'description' => 'Items that require careful handling.'],
            ['name' => 'Heavy Items', 'description' => 'Large or heavy products that cost more to ship.'],
        ];

        foreach ($classes as $class) {
            ShippingClass::firstOrCreate(['name' => $class['name']], $class);
        }

        $zones = [
            [
                'name' => 'Default Zone',
                'description' => 'Applies to all countries',
                'country' => '*',
                'state' => '*',
                'city' => '*',
                'postal_code' => '*',
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::firstOrCreate(['name' => $zone['name']], $zone);
        }

        $methods = [
            ['name' => 'Standard Shipping', 'description' => 'Delivered in 5–7 business days.', 'enabled' => true],
            ['name' => 'Express Shipping', 'description' => 'Delivered in 1–3 business days.', 'enabled' => true],
            ['name' => 'Free Shipping', 'description' => 'Available for specific products or zones.', 'enabled' => true],
        ];

        foreach ($methods as $method) {
            ShippingMethod::firstOrCreate(['name' => $method['name']], $method);
        }

        $defaultZone = ShippingZone::where('name', 'Default Zone')->first();

        $standardMethod = ShippingMethod::where('name', 'Standard Shipping')->first();
        $expressMethod = ShippingMethod::where('name', 'Express Shipping')->first();
        $freeMethod = ShippingMethod::where('name', 'Free Shipping')->first();

        $standardClass = ShippingClass::where('name', 'Standard Goods')->first();
        $fragileClass = ShippingClass::where('name', 'Fragile Items')->first();
        $heavyClass = ShippingClass::where('name', 'Heavy Items')->first();

        $rates = [
            [
                'name' => 'Global Standard Rate',
                'shipping_zone_id' => $defaultZone->id,
                'shipping_method_id' => $standardMethod->id,
                'shipping_class_id' => null,
                'type' => 'per_item',
                'is_percentage' => false,
                'cost' => 5.00,
            ],
            [
                'name' => 'Global Express Rate',
                'shipping_zone_id' => $defaultZone->id,
                'shipping_method_id' => $expressMethod->id,
                'shipping_class_id' => null,
                'type' => 'per_item',
                'is_percentage' => false,
                'cost' => 10.00,
            ],
            [
                'name' => 'Free Shipping (Global)',
                'shipping_zone_id' => $defaultZone->id,
                'shipping_method_id' => $freeMethod->id,
                'shipping_class_id' => null,
                'type' => 'per_item',
                'is_percentage' => false,
                'cost' => 0.00,
            ],
        ];

        foreach ($rates as $rate) {
            ShippingRate::firstOrCreate(['name' => $rate['name']], $rate);
        }

        $this->command->info('Shipping Data Seeded Successfully.');
    }
}
