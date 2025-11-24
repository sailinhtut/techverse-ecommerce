<?php

namespace App\Tax\Controllers;

use App\Cart\Models\Cart;
use App\Inventory\Models\Product;
use App\Tax\Models\TaxClass;
use App\Tax\Models\TaxZone;
use App\Tax\Models\TaxRate;
use App\Tax\Services\TaxRateService;
use Exception;
use Illuminate\Http\Request;

class TaxRateController
{

    public function calculateTaxCost(Request $request)
    {
        $validated = $request->validate([
            'shipping_address.country' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
        ], [
            'shipping_address.country.required' => 'Please select your country for shipping.',
            'shipping_address.state.required' => 'Please provide your state or region.',
            'shipping_address.city.required' => 'Please provide your city.',
            'shipping_address.postal_code.required' => 'Please provide your postal or ZIP code.',
        ]);

        $address = $validated['shipping_address'];

        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->where('is_checked_out', false)
            ->first();

        if (!$cart || !$cart->items()->exists()) {
            return response()->json(['message' => "Your cart is empty"], 400);
        }

        $calculated_tax_cost = TaxRateService::calculateTax($address, $cart->items);


        return response()->json([
            'data' => [
                'tax_cost' => $calculated_tax_cost
            ]
        ]);
    }

    public function viewAdminTaxRateListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = TaxRate::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            switch ($sortBy) {
                case 'last_updated':
                    $query->orderBy('updated_at', $orderBy)
                        ->orderBy('id', $orderBy);
                    break;

                case 'last_created':
                    $query->orderBy('created_at', $orderBy)->orderBy('id', $orderBy);
                    break;

                default:
                    $query->orderBy('updated_at', 'desc')
                        ->orderBy('id', 'desc');
            }

            $tax_rates = $query->paginate($perPage);
            $tax_rates->appends(request()->query());

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
                'type' => 'required|string|in:per_item,per_quantity,per_weight',
            ]);

            $is_percentage = $request->boolean('is_percentage', true);

            TaxRate::create([
                'name' => $validated['name'] ?? '',
                'description' => $validated['description'] ?? null,
                'tax_zone_id' => $validated['tax_zone_id'] ?? null,
                'tax_class_id' => $validated['tax_class_id'] ?? null,
                'is_percentage' => $is_percentage,
                'rate' => $validated['rate'],
                'type' => $validated['type'],
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
                'type' => 'nullable|string|in:per_item,per_quantity,per_weight',
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
                'type' => $validated['type'] ?? $rate->type,
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


    public function deleteSelectedTaxRates(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No rates selected for deletion.');
            }

            $rates = TaxRate::whereIn('id', $ids)->get();

            foreach ($rates as $rate) {
                $rate->delete();
            }

            return redirect()->back()->with('success', 'Selected rates deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected rates.");
        }
    }


    public function deleteAllTaxRates()
    {
        try {
            $rates = TaxRate::all();

            foreach ($rates as $rate) {
                $rate->delete();
            }

            return redirect()->back()->with('success', 'All rates deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all rates.");
        }
    }
}
