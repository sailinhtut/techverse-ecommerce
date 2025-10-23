<?php

namespace App\Shipping\Controllers;

use App\Inventory\Models\Product;
use App\Shipping\Models\ShippingMethod;
use App\Shipping\Models\ShippingRate;
use App\Shipping\Models\ShippingZone;
use Exception;
use Illuminate\Http\Request;

class ShippingMethodController
{
    public function viewAdminShippingMethodListPage()
    {
        try {
            $shipping_methods = ShippingMethod::orderBy('id', 'desc')->paginate(10);

            $shipping_methods->getCollection()->transform(function ($method) {
                return $method->jsonResponse();
            });

            return view('pages.admin.dashboard.shipping.shipping_method_list', [
                'shipping_methods' => $shipping_methods
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function filterShippingMethod(Request $request)
    {
        $validated = $request->validate([
            'cart_items' => 'required|array|min:1',
            'shipping_address.country' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
        ], [
            'cart_items.required' => 'Your cart is empty. Please add at least one item before proceeding.',
            'cart_items.array' => 'Cart items must be sent as a valid array.',
            'cart_items.min' => 'Your cart must contain at least one item.',

            'shipping_address.country.required' => 'Please select your country for shipping.',
            'shipping_address.state.required' => 'Please provide your state or region.',
            'shipping_address.city.required' => 'Please provide your city.',
            'shipping_address.postal_code.required' => 'Please provide your postal or ZIP code.',
        ]);

        $address = $validated['shipping_address'];

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

        $result = [];

        foreach ($methods as $method) {
            $method_total_cost = 0;


            foreach ($validated['cart_items'] as $item) {
                $product = Product::findOrFail($item['id']);

                $zone_rates = ShippingRate::where(function ($q) use ($zone) {
                    $q->where('shipping_zone_id', $zone->id)
                        ->orWhereNull('shipping_zone_id');
                })->get();

                // $result[$product['name']][$method->name]['debug_zone_rates'] = $zone_rates->map(fn($e) => $e->jsonResponse());

                if ($zone_rates->isEmpty()) continue;

                $method_rates = $zone_rates->filter(
                    fn($zone_rate) => $zone_rate->shipping_method_id == $method->id || is_null($zone_rate->shipping_method_id)
                );

                // $result[$product['name']][$method->name]['debug_method_rates'] = $method_rates->map(fn($e) => $e->jsonResponse());

                if ($method_rates->isEmpty()) continue;

                $class_rates = $method_rates->filter(
                    fn($r) => $r->shipping_class_id == ($product['shipping_class_id'] ?? null) || is_null($r->shipping_class_id)
                );

                // $result[$product['name']][$method->name]['debug_class_rates'] = $class_rates->map(fn($e) => $e->jsonResponse());

                if ($class_rates->isEmpty()) continue;

                if (is_null($product['shipping_class_id'])) {
                    $result['aborted_product'][$method->name][] = $product['name'];
                    continue;
                }

                $matched_rate = $class_rates->first();
                $method_total_cost += $matched_rate->calculateCost(array_merge($item, ['price' => $item['price']]));
            }

            if ($method_total_cost > 0 || $method->is_free) {
                $result['data'][] = [
                    'method' => $method->jsonResponse(),
                    'zone' => $zone->jsonResponse(),
                    'total_cost' => $method_total_cost,
                ];
            }
        }

        if (empty($result)) {
            return response()->json(['message' => 'No Shipping Method Available For This Order'], 404);
        }

        return response()->json($result);
    }

    public function createMethod(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'enabled' => 'nullable|boolean',
                'is_free' => 'nullable|boolean',
            ]);

            $enabled = $request->boolean('enabled', false);
            $is_free = $request->boolean('is_free', false);

            ShippingMethod::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'enabled' => $enabled,
                'is_free' => $is_free
            ]);

            return redirect()->back()->with('success', 'Shipping Method created successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateMethod(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'enabled' => 'nullable|boolean',
            ]);

            $method = Shippingmethod::find($id);

            if (!$method) abort(404, 'No Shipping Method Found');

            $enabled = $request->boolean('enabled', $method->enabled);
            $is_free = $request->boolean('is_free', $method->is_free);


            $method->update([
                'name' => $validated['name'] ?? $method->name,
                'description' => $validated['description'],
                'enabled' => $enabled,
                'is_free' => $is_free
            ]);

            return redirect()->back()->with('success', 'Shipping Method updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteMethod($id)
    {
        try {
            $method = Shippingmethod::find($id);

            if (!$method) abort(404, 'No Shipping Method Found');

            $method->delete();

            return redirect()->back()->with('success', 'Shipping Method deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
