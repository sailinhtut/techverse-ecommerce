<?php

namespace Database\Seeders;

use App\Tax\Models\TaxClass;
use App\Tax\Models\TaxRate;
use App\Tax\Models\TaxZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Standard Tax Rate', 'description' => 'Default tax class for general products.'],
            ['name' => 'Special Tax Rate', 'description' => 'Items that require careful handling.'],
        ];

        foreach ($classes as $class) {
            TaxClass::firstOrCreate(['name' => $class['name']], $class);
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
            TaxZone::firstOrCreate(['name' => $zone['name']], $zone);
        }

        $defaultZone = TaxZone::where('name', 'Default Zone')->first();

        $standardClass = TaxClass::where('name', 'Standard Tax Rate')->first();
        $specialClass = TaxClass::where('name', 'Special Tax Rate')->first();

        $rates = [
            [
                'name' => 'Global Standard Tax Rate',
                'tax_zone_id' => $defaultZone->id,
                'tax_class_id' => $standardClass->id,
                'is_percentage' => false,
                'rate' => 5.00,
                'type' => 'per_item',
            ],
            [
                'name' => 'Global Special Tax Rate',
                'tax_zone_id' => $defaultZone->id,
                'tax_class_id' => $specialClass->id,
                'is_percentage' => false,
                'rate' => 10.00,
                'type' => 'per_item',
            ],
        ];

        foreach ($rates as $rate) {
            TaxRate::firstOrCreate(['name' => $rate['name']], $rate);
        }

        $this->command->info('Tax Data Seeded Successfully.');
    }
}
