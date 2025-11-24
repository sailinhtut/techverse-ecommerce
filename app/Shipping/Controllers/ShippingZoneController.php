<?php

namespace App\Shipping\Controllers;

use App\Shipping\Models\ShippingZone;
use Exception;
use Illuminate\Http\Request;

class ShippingZoneController
{
    public function viewAdminShippingZoneListPage(Request $request)
    {
        try {

            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = ShippingZone::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhere('state', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('postal_code', 'like', "%{$search}%")
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

            $shipping_zones = $query->paginate($perPage);
            $shipping_zones->appends(request()->query());

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


    public function deleteSelectedShippingZones(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No zones selected for deletion.');
            }

            $zones = ShippingZone::whereIn('id', $ids)->get();

            foreach ($zones as $zone) {
                $zone->delete();
            }

            return redirect()->back()->with('success', 'Selected zones deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected zones.");
        }
    }


    public function deleteAllShippingZones()
    {
        try {
            $zones = ShippingZone::all();

            foreach ($zones as $zone) {
                $zone->delete();
            }

            return redirect()->back()->with('success', 'All zones deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all zones.");
        }
    }
}
