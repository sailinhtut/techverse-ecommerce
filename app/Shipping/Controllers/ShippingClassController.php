<?php

namespace App\Shipping\Controllers;

use App\Shipping\Models\ShippingClass;
use Exception;
use Illuminate\Http\Request;

class ShippingClassController
{
    public function viewAdminShippingClassListPage(Request $request)
    {
        try {

            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = ShippingClass::query();

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

            $shipping_classes = $query->paginate($perPage);
            $shipping_classes->appends(request()->query());

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

    public function deleteSelectedShippingClasses(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No classes selected for deletion.');
            }

            $classes = ShippingClass::whereIn('id', $ids)->get();

            foreach ($classes as $class) {
                $class->delete();
            }

            return redirect()->back()->with('success', 'Selected classes deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected classes.");
        }
    }


    public function deleteAllShippingClasses()
    {
        try {
            $classes = ShippingClass::all();

            foreach ($classes as $class) {
                $class->delete();
            }

            return redirect()->back()->with('success', 'All classes deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all classes.");
        }
    }
}
