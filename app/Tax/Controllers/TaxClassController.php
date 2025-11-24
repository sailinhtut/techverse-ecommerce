<?php

namespace App\Tax\Controllers;

use App\Tax\Models\TaxClass;
use Exception;
use Illuminate\Http\Request;

class TaxClassController
{
    public function viewAdminTaxClassListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = TaxClass::query();

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

            $tax_classes = $query->paginate($perPage);
            $tax_classes->appends(request()->query());

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

    public function deleteSelectedTaxClasses(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No classes selected for deletion.');
            }

            $classes = TaxClass::whereIn('id', $ids)->get();

            foreach ($classes as $class) {
                $class->delete();
            }

            return redirect()->back()->with('success', 'Selected classes deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected classes.");
        }
    }


    public function deleteAllTaxClasses()
    {
        try {
            $classes = TaxClass::all();

            foreach ($classes as $class) {
                $class->delete();
            }

            return redirect()->back()->with('success', 'All classes deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all classes.");
        }
    }
}
