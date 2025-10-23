<?php

namespace App\Shipping\Controllers;

use App\Shipping\Models\ShippingClass;
use Exception;
use Illuminate\Http\Request;

class ShippingClassController
{
    public function viewAdminShippingClassListPage()
    {
        try {
            $shipping_classes = ShippingClass::orderBy('id', 'desc')->paginate(10);

            $shipping_classes->getCollection()->transform(function ($class) {
                return $class->jsonResponse();
            });

            return view('pages.admin.dashboard.shipping.shipping_class_list', [
                'shipping_classes' => $shipping_classes
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createClass(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
            ]);

            ShippingClass::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Shipping Class created successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateClass(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:100',
                'description' => 'nullable|string',
            ]);

            $class = ShippingClass::find($id);

            if (!$class) abort(404, 'No Shipping Class Found');

            $class->update([
                'name' => $validated['name'] ?? $class->name,
                'description' => $validated['description'] ?? $class->description,
            ]);

            return redirect()->back()->with('success', 'Shipping Class updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteClass($id)
    {
        try {
            $class = ShippingClass::find($id);

            if (!$class) abort(404, 'No Shipping Class Found');

            $class->delete();

            return redirect()->back()->with('success', 'Shipping Class deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
