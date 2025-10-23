<?php

namespace App\Tax\Controllers;

use App\Tax\Models\TaxZone;
use Exception;
use Illuminate\Http\Request;

class TaxZoneController
{
    public function viewAdminTaxZoneListPage()
    {
        try {
            $tax_zones = TaxZone::orderBy('id', 'desc')->paginate(10);

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
}
