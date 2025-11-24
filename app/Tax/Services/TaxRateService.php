<?php

namespace App\Tax\Services;

use App\Inventory\Models\Product;
use App\Tax\Models\TaxRate;
use App\Tax\Models\TaxZone;
use Exception;

class TaxRateService
{
    public static function calculateTax($shipping_address, $cart_items)
    {
        try {
            $address = $shipping_address;
            $country_zones = TaxZone::where('country', $address['country'])
                ->orWhere('country', '*')
                ->get();

            if ($country_zones->isEmpty()) {
                throw (new Exception("No Tax Available In Your Region ({$address['country']})"));
            }

            $state_zones = $country_zones->whereIn('state', [$address['state'], '*']);
            if ($state_zones->isEmpty()) {
                throw (new Exception("No Tax Available In Your Region ({$address['country']},{$address['state']})"));
            }

            $city_zones = $state_zones->whereIn('city', [$address['city'], '*']);
            if ($city_zones->isEmpty()) {
                throw (new Exception("No Tax Available In Your Region ({$address['country']},{$address['state']},{$address['city']})"));
            }

            $postal_zones = $city_zones->whereIn('postal_code', [$address['postal_code'], '*']);
            if ($postal_zones->isEmpty()) {
                throw (new Exception("No Tax Available In Your Region ({$address['country']},{$address['state']},{$address['city']},{$address['postal_code']})"));
            }

            $zone = $postal_zones->first();

            $tax_total_cost = 0;

            foreach ($cart_items as $item) {
                $product = Product::findOrFail($item->product->id);

                $zone_rates = TaxRate::where(function ($q) use ($zone) {
                    $q->where('tax_zone_id', $zone->id)
                        ->orWhereNull('tax_zone_id');
                })->get();

                // $result[$product['name']]['debug_zone_rates'] = $zone_rates->map(fn($e) => $e->jsonResponse());

                if ($zone_rates->isEmpty()) continue;

                $class_rates = $zone_rates->filter(
                    fn($zr) => $zr->tax_class_id == ($product['tax_class_id'] ?? null) || is_null($zr->tax_class_id)
                );

                // $result[$product['name']]['debug_class_rates'] = $class_rates->map(fn($e) => $e->jsonResponse());

                if ($class_rates->isEmpty()) continue;

                if (is_null($product['tax_class_id'])) {
                    continue;
                }

                $matched_rate = null;

                if ($class_rates->count() > 1) {
                    $matched_rate = $class_rates->whereNotNull('tax_class_id')->first();
                } else {
                    $matched_rate = $class_rates->first();
                }

                $matched_rate = $class_rates->first();
                $tax_total_cost += $matched_rate->calculateTax($item);

                // $result['debug_tax_cost'][$product['name']] = $matched_rate->calculateTax($item);
            }

            return $tax_total_cost;
        } catch (Exception $e) {
            throw ($e);
        }
    }
}
