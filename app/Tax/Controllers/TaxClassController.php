<?php

namespace App\Tax\Controllers;

use App\Tax\Models\TaxClass;
use Exception;
use Illuminate\Http\Request;

class TaxClassController
{
    public function viewAdminTaxClassListPage()
    {
        try {
            $tax_classes = TaxClass::orderBy('id', 'desc')->paginate(10);

            $tax_classes->getCollection()->transform(function ($class) {
                return $class->jsonResponse();
            });

            return view('pages.admin.dashboard.tax.tax_class_list', [
                'tax_classes' => $tax_classes
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

            TaxClass::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Tax Class created successfully.');
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

            $class = TaxClass::find($id);

            if (!$class) abort(404, 'No Tax Class Found');

            $class->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            return redirect()->back()->with('success', 'Tax Class updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteClass($id)
    {
        try {
            $class = TaxClass::find($id);

            if (!$class) abort(404, 'No Tax Class Found');

            $class->delete();

            return redirect()->back()->with('success', 'Tax Class deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
