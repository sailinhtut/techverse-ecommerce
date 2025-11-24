<?php

namespace App\Tax\Controllers;

use App\Tax\Models\TaxZone;
use Exception;
use Illuminate\Http\Request;

class TaxZoneController
{
    public function viewAdminTaxZoneListPage(Request $request)
    {
        try {

            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = TaxZone::query();

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

            $tax_zones = $query->paginate($perPage);
            $tax_zones->appends(request()->query());

            $tax_zones->getCollection()->transform(function ($zone) {
                return $zone->jsonResponse();
            });

            return view('pages.admin.dashboard.tax.tax_zone_list', [
                'tax_zones' => $tax_zones
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
                'state' => 'nullable|string',
                'city' => 'nullable|string',
                'postal_code' => 'nullable|string',
            ]);

            TaxZone::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'country' => $validated['country'],
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Tax Zone created successfully.');
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

            $zone = TaxZone::find($id);

            if (!$zone) abort(404, 'No Tax Zone Found');

            $zone->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'country' => $validated['country'],
                'state' => $validated['state'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
            ]);

            return redirect()->back()->with('success', 'Tax Zone updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteZone($id)
    {
        try {
            $zone = TaxZone::find($id);

            if (!$zone) abort(404, 'No Tax Zone Found');

            $zone->delete();

            return redirect()->back()->with('success', 'Tax Zone deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteSelectedTaxZones(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No zones selected for deletion.');
            }

            $zones = TaxZone::whereIn('id', $ids)->get();

            foreach ($zones as $zone) {
                $zone->delete();
            }

            return redirect()->back()->with('success', 'Selected zones deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected zones.");
        }
    }


    public function deleteAllTaxZones()
    {
        try {
            $zones = TaxZone::all();

            foreach ($zones as $zone) {
                $zone->delete();
            }

            return redirect()->back()->with('success', 'All zones deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all zones.");
        }
    }
}
