<?php

namespace App\Shipping\Services;

use App\Inventory\Models\Product;
use App\Shipping\Models\ShippingMethod;
use App\Shipping\Models\ShippingRate;
use App\Shipping\Models\ShippingZone;
use Exception;
use Illuminate\Support\Facades\Log;

class ShippingMethodService
{
    public static function calculateShippingMethods(array $shipping_address,  $cart_items)
    {
        try {
            $address = $shipping_address;

            $country_zones = ShippingZone::where('country', $address['country'])
                ->orWhere('country', '*')
                ->get();

            if ($country_zones->isEmpty()) {
                return response()->json(['message' => "No Shipping Method Available In Your Region ({$address['country']})"], 404);
            }

            $state_zones = $country_zones->whereIn('state', [$address['state'], '*']);
            if ($state_zones->isEmpty()) {
                return response()->json(['message' => "No Shipping Method Available In Your Region ({$address['country']},{$address['state']})"], 404);
            }

            $city_zones = $state_zones->whereIn('city', [$address['city'], '*']);
            if ($city_zones->isEmpty()) {
                return response()->json(['message' => "No Shipping Method Available In Your Region ({$address['country']},{$address['state']},{$address['city']})"], 404);
            }

            $postal_zones = $city_zones->whereIn('postal_code', [$address['postal_code'], '*']);
            if ($postal_zones->isEmpty()) {
                return response()->json(['message' => "No Shipping Method Available In Your Region ({$address['country']},{$address['state']},{$address['city']},{$address['postal_code']})"], 404);
            }

            $zone = $postal_zones->first();

            $methods = ShippingMethod::where('enabled', true)->get();



            $caculated_methods = [];


            foreach ($methods as $method) {
                $method_total_cost = 0;
                foreach ($cart_items as $item) {
                    $product = Product::findOrFail($item->product->id);

                    $zone_rates = ShippingRate::where(function ($q) use ($zone) {
                        $q->where('shipping_zone_id', $zone->id)
                            ->orWhereNull('shipping_zone_id');
                    })->get();


                    if ($zone_rates->isEmpty()) continue;

                    $method_rates = $zone_rates->filter(
                        fn($zone_rate) => $zone_rate->shipping_method_id == $method->id || is_null($zone_rate->shipping_method_id)
                    );

                    if ($method_rates->isEmpty()) continue;

                    $class_rates = $method_rates->filter(
                        fn($r) => $r->shipping_class_id == ($product['shipping_class_id'] ?? null) || is_null($r->shipping_class_id)
                    );


                    if ($class_rates->isEmpty()) continue;

                    if (is_null($product['shipping_class_id'])) {

                        continue;
                    }

                    $matched_rate = null;

                    if ($class_rates->count() > 1) {
                        $matched_rate = $class_rates->whereNotNull('shipping_class_id')->first();
                    } else {
                        $matched_rate = $class_rates->first();
                    }

                    $method_total_cost += $matched_rate->calculateCost([
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'weight' => $item->product->weight
                    ]);

                    // $result['debug_shipping_cost'][$method->name][$product['name']] = $matched_rate->calculateCost(array_merge([
                    //     'price' => $item['price'],
                    //     'quantity' => $item['quantity'],
                    //     'weight' => $item['product']['weight']
                    // ]));
                }

                if ($method_total_cost > 0 || $method->is_free) {
                    $caculated_methods[] = array_merge(
                        $method->jsonResponse(),
                        ['shipping_cost' =>  $method_total_cost]
                    );
                }
            }

            return $caculated_methods;
        } catch (Exception $e) {
            throw ($e);
        }
    }
}
