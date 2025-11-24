<?php

namespace App\Shipping\Controllers;

use App\Inventory\Models\Product;
use App\Shipping\Models\ShippingClass;
use App\Shipping\Models\ShippingMethod;
use App\Shipping\Models\ShippingRate;
use App\Shipping\Models\ShippingZone;
use App\Tax\Models\TaxRate;
use App\Tax\Models\TaxZone;
use Exception;
use Illuminate\Http\Request;

class ShippingRateController
{
    public function viewAdminShippingRateListPage(Request $request)
    {
        try {

            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = ShippingRate::query();

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

            $shipping_rates = $query->paginate($perPage);
            $shipping_rates->appends(request()->query());

            $shipping_rates->getCollection()->transform(function ($rate) {
                return $rate->jsonResponse(['zone', 'method', 'class']);
            });

            $shipping_classes = ShippingClass::orderBy('id', 'desc')->get();
            $shipping_zones = ShippingZone::orderBy('id', 'desc')->get();
            $shipping_methods = ShippingMethod::orderBy('id', 'desc')->get();

            return view('pages.admin.dashboard.shipping.shipping_rate_list', [
                'shipping_rates' => $shipping_rates,
                'shipping_classes' => $shipping_classes,
                'shipping_zones' => $shipping_zones,
                'shipping_methods' => $shipping_methods,
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
                'shipping_zone_id' => 'nullable|exists:shipping_zones,id',
                'shipping_method_id' => 'nullable|exists:shipping_methods,id',
                'shipping_class_id' => 'nullable|exists:shipping_classes,id',
                'type' => 'required|in:per_item,per_quantity,per_weight',
                'is_percentage' => 'required|boolean',
                'cost' => 'required|numeric|min:0',
            ]);

            $is_percentage = $request->boolean('is_percentage', false);

            ShippingRate::create([
                'name' => $validated['name'] ?? '',
                'description' => $validated['description'],
                'shipping_zone_id' => $validated['shipping_zone_id'] ?? null,
                'shipping_method_id' => $validated['shipping_method_id'] ?? null,
                'shipping_class_id' => $validated['shipping_class_id'] ?? null,
                'type' => $validated['type'],
                'is_percentage' => $is_percentage,
                'cost' => $validated['cost'],
            ]);

            return redirect()->back()->with('success', 'Shipping rate created successfully.');
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

                'shipping_zone_id' => 'nullable|exists:shipping_zones,id',
                'shipping_method_id' => 'nullable|exists:shipping_methods,id',
                'shipping_class_id' => 'nullable|exists:shipping_classes,id',

                'type' => 'nullable|in:per_item,per_quantity,per_weight',
                'is_percentage' => 'nullable|boolean',
                'cost' => 'nullable|numeric|min:0',
            ]);

            $rate = ShippingRate::find($id);

            $is_percentage = $request->boolean('is_percentage', $rate->is_percentage);

            if (!$rate) abort(404, 'No Shipping Rate Found');
            $rate->update([
                'name' => $validated['name'] ?? $rate->name,
                'description' => $validated['description'] ?? $rate->description,
                'shipping_zone_id' => $validated['shipping_zone_id'] ?? null,
                'shipping_method_id' => $validated['shipping_method_id'] ?? null,
                'shipping_class_id' => $validated['shipping_class_id'] ?? null,
                'type' => $validated['type'] ?? $rate->type,
                'is_percentage' => $is_percentage,
                'cost' => $validated['cost'] ?? $rate->cost
            ]);

            $rate->save();


            return redirect()->back()->with('success', 'Shipping Rate Updated Successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteRate($id)
    {
        try {
            $rate = ShippingRate::findOrFail($id);
            $rate->delete();

            return redirect()->back()->with('success', 'Shipping rate deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function deleteSelectedShippingRates(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No rates selected for deletion.');
            }

            $rates = ShippingRate::whereIn('id', $ids)->get();

            foreach ($rates as $rate) {
                $rate->delete();
            }

            return redirect()->back()->with('success', 'Selected rates deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected rates.");
        }
    }

    public function deleteAllShippingRates()
    {
        try {
            $rates = ShippingRate::all();

            foreach ($rates as $rate) {
                $rate->delete();
            }

            return redirect()->back()->with('success', 'All rates deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all rates.");
        }
    }
}
