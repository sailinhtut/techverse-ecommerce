<?php

namespace App\Inventory\Controllers; 
use App\Inventory\Models\ProductInventoryLog;
use Exception;
use Illuminate\Http\Request;

class ProductInventoryLogController
{
    public function viewAdminInventoryLogPage(Request $request)
    {
        try {
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query');
            $productId = $request->get('product_id');
            $actionType = $request->get('action_type');

            $query = ProductInventoryLog::with(['product', 'creator']);

            if ($search) {
                $query->where('note', 'like', "%{$search}%")
                    ->orWhere('reference_type', 'like', "%{$search}%");
            }

            if ($productId) {
                $query->where('product_id', $productId);
            }

            if ($actionType) {
                $query->where('action_type', $actionType);
            }

            $query->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');

            $logs = $query->paginate($perPage);
            $logs->appends($request->query());

            $logs->getCollection()->transform(
                fn($log) => $log->jsonResponse(['product', 'creator'])
            );

            return view('pages.admin.dashboard.inventory.inventory_log_list', [
                'logs' => $logs,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

 
}
