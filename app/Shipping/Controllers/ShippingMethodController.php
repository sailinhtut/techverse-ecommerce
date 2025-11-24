<?php

namespace App\Shipping\Controllers;

use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use App\Inventory\Models\Product;
use App\Shipping\Models\ShippingMethod;
use App\Shipping\Models\ShippingRate;
use App\Shipping\Models\ShippingZone;
use App\Shipping\Services\ShippingMethodService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingMethodController
{
    public function viewAdminShippingMethodListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = ShippingMethod::query();

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

            $shipping_methods = $query->paginate($perPage);
            $shipping_methods->appends(request()->query());

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

        $calculated_methods = ShippingMethodService::calculateShippingMethods($address, $cart->items);

        if (empty($calculated_methods)) {
            return response()->json(['message' => 'No Shipping Method Available For This Order'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $calculated_methods
        ]);
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


    public function deleteSelectedShippingMethods(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No methods selected for deletion.');
            }

            $methods = ShippingMethod::whereIn('id', $ids)->get();

            foreach ($methods as $method) {
                $method->delete();
            }

            return redirect()->back()->with('success', 'Selected methods deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected methods.");
        }
    }


    public function deleteAllShippingMethods()
    {
        try {
            $methods = ShippingMethod::all();

            foreach ($methods as $method) {
                $method->delete();
            }

            return redirect()->back()->with('success', 'All methods deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all methods.");
        }
    }
}
