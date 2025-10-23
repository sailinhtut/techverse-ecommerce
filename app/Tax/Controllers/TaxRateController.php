<?php

namespace App\Tax\Controllers;

use App\Inventory\Models\Product;
use App\Tax\Models\TaxClass;
use App\Tax\Models\TaxZone;
use App\Tax\Models\TaxRate;
use Exception;
use Illuminate\Http\Request;

class TaxRateController
{

    public function calculateTaxCost(Request $request)
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

        $country_zones = TaxZone::where('country', $address['country'])
            ->orWhere('country', '*')
            ->get();

        if ($country_zones->isEmpty()) {
            return response()->json(['message' => "No Tax Available In Your Region ({$address['country']})"], 404);
        }

        $state_zones = $country_zones->whereIn('state', [$address['state'], '*']);
        if ($state_zones->isEmpty()) {
            return response()->json(['message' => "No Tax Available In Your Region ({$address['country']},{$address['state']})"], 404);
        }

        $city_zones = $state_zones->whereIn('city', [$address['city'], '*']);
        if ($city_zones->isEmpty()) {
            return response()->json(['message' => "No Tax Available In Your Region ({$address['country']},{$address['state']},{$address['city']})"], 404);
        }

        $postal_zones = $city_zones->whereIn('postal_code', [$address['postal_code'], '*']);
        if ($postal_zones->isEmpty()) {
            return response()->json(['message' => "No Tax Available In Your Region ({$address['country']},{$address['state']},{$address['city']},{$address['postal_code']})"], 404);
        }

        $zone = $postal_zones->first();

        $result = [];

        $tax_total_cost = 0;

        foreach ($validated['cart_items'] as $item) {
            $product = Product::findOrFail($item['id']);

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

            if(is_null($product['tax_class_id'])){
                continue;
            }

            $item_quantity = intval($item['quantity']);
            $item_price = $product['regular_price'] ?? 0;
            $item_cost =  $item_quantity * $item_price;

            $matched_rate = $class_rates->first();
            $tax_total_cost += $matched_rate->calculateTax($item_cost);
        }

        $result['data'] = [
            'zone' => $zone->jsonResponse(),
            'total_cost' => $tax_total_cost,
        ];

        if (empty($result)) {
            return response()->json(['message' => 'No Tax Available For This Order'], 404);
        }

        return response()->json($result);
    }

    public function viewAdminTaxRateListPage()
    {
        try {
            $tax_rates = TaxRate::orderBy('updated_at', 'desc')->paginate(10);

            $tax_rates->getCollection()->transform(function ($rate) {
                return $rate->jsonResponse(['zone', 'class']);
            });

            $tax_classes = TaxClass::orderBy('id', 'desc')->get();
            $tax_zones = TaxZone::orderBy('id', 'desc')->get();

            return view('pages.admin.dashboard.tax.tax_rate_list', [
                'tax_rates' => $tax_rates,
                'tax_classes' => $tax_classes,
                'tax_zones' => $tax_zones,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createRate(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'tax_zone_id' => 'nullable|exists:tax_zones,id',
                'tax_class_id' => 'nullable|exists:tax_classes,id',
                'is_percentage' => 'required|boolean',
                'rate' => 'required|numeric|min:0',
            ]);

            $is_percentage = $request->boolean('is_percentage', true);

            TaxRate::create([
                'name' => $validated['name'] ?? '',
                'description' => $validated['description'] ?? null,
                'tax_zone_id' => $validated['tax_zone_id'] ?? null,
                'tax_class_id' => $validated['tax_class_id'] ?? null,
                'is_percentage' => $is_percentage,
                'rate' => $validated['rate'],
            ]);

            return redirect()->back()->with('success', 'Tax Rate created successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateRate(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'tax_zone_id' => 'nullable|exists:tax_zones,id',
                'tax_class_id' => 'nullable|exists:tax_classes,id',
                'is_percentage' => 'nullable|boolean',
                'rate' => 'nullable|numeric|min:0',
            ]);

            $rate = TaxRate::find($id);

            if (!$rate) abort(404, 'No Tax Rate Found');

            $is_percentage = $request->boolean('is_percentage', $rate->is_percentage);

            $rate->update([
                'name' => $validated['name'] ?? $rate->name,
                'description' => $validated['description'] ?? null,
                'tax_zone_id' => $validated['tax_zone_id'] ?? null,
                'tax_class_id' => $validated['tax_class_id'] ?? null,
                'is_percentage' => $is_percentage,
                'rate' => $validated['rate'] ?? $rate->rate,
            ]);

            return redirect()->back()->with('success', 'Tax Rate updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteRate($id)
    {
        try {
            $rate = TaxRate::find($id);

            if (!$rate) abort(404, 'No Tax Rate Found');

            $rate->delete();

            return redirect()->back()->with('success', 'Tax Rate deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
