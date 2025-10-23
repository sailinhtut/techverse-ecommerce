<?php

namespace App\Shipping\Controllers;

use App\Shipping\Models\ShippingZone;
use Exception;
use Illuminate\Http\Request;

class ShippingZoneController
{
    public function viewAdminShippingZoneListPage()
    {
        try {
            $shipping_zones = ShippingZone::orderBy('id', 'desc')->paginate(10);

            $shipping_zones->getCollection()->transform(function ($zone) {
                return $zone->jsonResponse();
            });

            return view('pages.admin.dashboard.shipping.shipping_zone_list', [
                'shipping_zones' => $shipping_zones
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createZone(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'country' => 'required|string',
                'state' => 'required|string',
                'city' => 'required|string',
                'postal_code' => 'required|string',
            ]);

            ShippingZone::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'country' => $validated['country'],
                'state' => $validated['state'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
            ]);

            return redirect()->back()->with('success', 'Shipping Zone created successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateZone(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'country' => 'nullable|string',
                'state' => 'nullable|string',
                'city' => 'nullable|string',
                'postal_code' => 'nullable|string',
            ]);

            $zone = ShippingZone::find($id);

            if (!$zone) abort(404, 'No Shipping Zone Found');


            $zone->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'country' => $validated['country'],
                'state' => $validated['state'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
            ]);

            $zone->save();


            return redirect()->back()->with('success', 'Shipping Zone updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteZone($id)
    {
        try {
            $zone = ShippingZone::find($id);

            if (!$zone) abort(404, 'No Shipping Zone Found');

            $zone->delete();

            return redirect()->back()->with('success', 'Shipping Zone deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
