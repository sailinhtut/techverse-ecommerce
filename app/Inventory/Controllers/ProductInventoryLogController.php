<?php

namespace App\Inventory\Controllers;

use App\Inventory\Models\Product;
use App\Inventory\Models\ProductInventoryLog;
use App\Inventory\Models\ProductVariant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductInventoryLogController
{
    public function viewAdminInventoryLogListPage(Request $request)
    {
        try {
            $sortBy  = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search  = $request->get('query', null);

            $query = ProductInventoryLog::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('note', 'like', "%{$search}%")
                        ->orWhere('reference_type', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('product_id', 'like', "%{$search}%");
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

            $logs = $query->paginate($perPage);
            $logs->appends($request->query());

            $logs->getCollection()
                ->transform(fn($log) => $log->jsonResponse(['product', 'productVariant', 'creator']));

            return view('pages.admin.dashboard.inventory_log.inventory_log_list', [
                'logs' => $logs,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminLogDetailPage(Request $request, string $id)
    {
        try {
            $log = ProductInventoryLog::findOrFail($id);

            return view('pages.admin.dashboard.inventory_log.inventory_log_detail', [
                'log' => $log->jsonResponse(),
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function searchProducts(Request $request)
    {
        $keyword = $request->input('q');

        if (!$keyword || strlen($keyword) < 2) {
            return response()->json([
                'data' => [],
                'message' => 'Enter at least 2 characters'
            ]);
        }

        $products = Product::select('id', 'name', 'slug', 'regular_price', 'stock')
            ->where('name', 'like', "%{$keyword}%")
            ->orWhere('slug', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();

        $data = [];

        foreach ($products as $product) {
            $product_variants = $product->productVariants;

            $data[] = [
                'id' => $product->id,
                'variant_id' => null,
                'name' => $product->name,
                'regular_price' => $product->regular_price,
                'stock' => $product->stock,
                'slug' => $product->slug
            ];

            if (!empty($product_variants)) {
                foreach ($product_variants as $v) {
                    $data[] = [
                        'id' => $product->id,
                        'variant_id' => $v->id,
                        'name' => $product->name . " [Variant-" . $v->sku . "]",
                        'regular_price' => $v->regular_price,
                        'stock' => $v->stock,
                        'slug' => $product->slug
                    ];
                }
            }
        }

        return response()->json([
            'data' => $data,
        ]);
    }


    public function createTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'variant_id' => 'nullable|exists:product_variants,id',
                'action_type' => 'required|in:in,out,reset',
                'quantity' => 'required|numeric',
                'note' => 'nullable|string',
            ]);


            $product = Product::findOrFail($validated['product_id']);
            $product_variant = isset($validated['variant_id']) ? ProductVariant::findOrFail($validated['variant_id']) : null;
            $product_name = $product_variant ? $product->name . " [Variant-" . $product_variant->sku . "]" : $product->name;

            $enable_stock = $product_variant ? $product_variant->enable_stock : $product->enable_stock;
            if (!$enable_stock) {

                abort(422, $product_name . ' does not support stock management. Please enable stock first.');
            }


            $original_stock = $product_variant ? $product_variant->stock : $product->stock;
            $updated_stock = 0;

            switch ($validated['action_type']) {
                case 'in':
                    $updated_stock = $original_stock + $validated['quantity'];
                    break;
                case 'out':
                    $updated_stock = $original_stock - $validated['quantity'];
                    $updated_stock = $updated_stock < 0 ? 0 : $updated_stock;
                    break;
                case 'reset':
                    $updated_stock = $validated['quantity'];
                    break;
                default:
                    $updated_stock = $original_stock + $validated['quantity'];
                    break;
            }

            DB::beginTransaction();

            if ($product_variant) {
                $product_variant->fill([
                    'stock' => $updated_stock
                ]);
                $product_variant->save();
            } else {
                $product->fill([
                    'stock' => $updated_stock
                ]);
                $product->save();
            }

            $new_log = ProductInventoryLog::create([
                'product_id' => $validated['product_id'],
                'variant_id' => $product_variant ? $product_variant->id : null,
                'action_type' => $validated['action_type'],
                'quantity' => $validated['quantity'],
                'note' => $validated['note'],
                'stock_before' => $original_stock,
                'stock_after' => $updated_stock,
                'reference_type' => 'manual',
                'reference_id' => null,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Product log created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrors($e);
        }
    }

    public function updateTransaction(Request $request, $id)
    {
        try {
            $log = ProductInventoryLog::findOrFail($id);

            $validated = $request->validate([
                'note'        => 'nullable|string',
            ]);

            $log->fill([
                "note" => $validated['note'],

            ]);
            $log->save();

            return redirect()->back()
                ->with('success', 'Product log updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteTransaction($id)
    {
        try {
            $log = ProductInventoryLog::findOrFail($id);
            $log->delete();
            return redirect()->back()
                ->with('success', 'Log deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteSelectedTransactions(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()
                    ->with('error', 'No logs selected for deletion.');
            }

            $logs = ProductInventoryLog::whereIn('id', $ids)->get();

            foreach ($logs as $log) {
                $log->delete();
            }

            return redirect()->back()
                ->with('success', 'Selected logs deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteAllTransactions()
    {
        try {
            $logs = ProductInventoryLog::all();

            foreach ($logs as $log) {
                $log->delete();
            }

            return redirect()->back()
                ->with('success', 'All logs deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
