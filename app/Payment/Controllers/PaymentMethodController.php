<?php

namespace App\Payment\Controllers;

use App\Cart\Models\Cart;
use App\Inventory\Models\Product;
use App\Payment\Models\PaymentMethod;
use App\Payment\Services\PaymentMethodService;
use Exception;
use Illuminate\Http\Request;

class PaymentMethodController
{
    public function viewAdminPaymentMethodListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = PaymentMethod::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
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

            $paymentmethods = $query->paginate($perPage);
            $paymentmethods->appends(request()->query());

            $paymentmethods->getCollection()->transform(function ($payment) {
                return $payment->jsonResponse(['paymentAttributes']);
            });

            return view('pages.admin.dashboard.payment.payment_method_list', [
                'paymentmethods' => $paymentmethods
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function filterPaymentMethod(Request $request)
    {
        try {
            $cart = Cart::with('items.product')
                ->where('user_id', auth()->id())
                ->orWhere('is_checked_out', false)
                ->first();

            if (!$cart || !$cart->items()->exists()) {
                return response()->json(['message' => "Your cart is empty"], 400);
            }

            $calculated_methods = PaymentMethodService::calculatePaymentMethods($cart->items);

            return response()->json([
                'success' => true,
                'data' => $calculated_methods,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createAdminCODPaymentMethod(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'enabled' => 'nullable|boolean',
                'description' => 'nullable|string',
                'priority' => 'required|in:high,low',
                'cod.instruction' => 'nullable|string|max:1000',
            ]);

            $method = PaymentMethod::create([
                'name' => $validated['name'],
                'code' => 'cod',
                'type' => 'manual',
                'priority' => $validated['priority'] ?? true,
                'enabled' => $request->boolean('enabled', true),
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['cod']['instruction'])) {
                $method->paymentAttributes()->create([
                    'key' => 'instruction',
                    'value' => $validated['cod']['instruction'],
                ]);
            }

            return back()->with('success', 'COD Payment Method Created Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createAdminDirectBankTransferPaymentMethod(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'enabled' => 'nullable|boolean',
                'description' => 'nullable|string',
                'priority' => 'required|in:high,low',
                'bank_accounts' => 'required|array|min:1',
                'bank_accounts.*.account_id' => 'required|string|max:255',
                'bank_accounts.*.account_name' => 'required|string|max:255',
                'bank_accounts.*.bank_name' => 'required|string|max:255',
                'bank_accounts.*.branch_name' => 'nullable|string|max:255',
            ]);

            $method = PaymentMethod::create([
                'name' => $validated['name'],
                'code' => 'direct_bank_transfer',
                'type' => 'manual',
                'priority' => $validated['priority'] ?? true,
                'enabled' => $request->boolean('enabled', true),
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['bank_accounts'] as $index => $bank) {
                foreach ($bank as $key => $value) {
                    $method->paymentAttributes()->create([
                        'key' => "bank_account_{$index}_{$key}",
                        'value' => $value,
                    ]);
                }
            }

            return back()->with('success', 'Direct Bank Transfer Method Created Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateAdminCODPaymentMethod(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'enabled' => 'nullable|boolean',
                'description' => 'nullable|string',
                'priority' => 'required|in:high,low',
                'cod.instruction' => 'nullable|string|max:1000',
            ]);

            $method = PaymentMethod::find($id);

            if (!$method) abort(404, 'No COD Payment Found');


            $method->update([
                'name' => $validated['name'] ?? $method->name,
                'enabled' => $request->boolean('enabled', $method->enabled),
                'description' => $validated['description'] ??
                    $method->description,
                'priority' => $validated['priority'] ?? true,
            ]);

            if (!empty($validated['cod']['instruction'])) {
                $method->paymentAttributes()->updateOrCreate(
                    ['key' => 'instruction'],
                    ['value' => $validated['cod']['instruction']]
                );
            }

            return back()->with('success', 'COD Payment Method Updated Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateAdminDirectBankTransferPaymentMethod(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'enabled' => 'nullable|boolean',
                'description' => 'nullable|string',
                'priority' => 'required|in:high,low',
                'bank_accounts' => 'nullable|array|min:1',
                'bank_accounts.*.account_id' => 'required_with:bank_accounts|string|max:255',
                'bank_accounts.*.account_name' => 'required_with:bank_accounts|string|max:255',
                'bank_accounts.*.bank_name' => 'required_with:bank_accounts|string|max:255',
                'bank_accounts.*.branch_name' => 'nullable|string|max:255',
            ]);

            $method = PaymentMethod::find($id);

            if (!$method) {
                abort(404, 'No Direct Bank Transfer Method Found');
            }

            $method->update([
                'name' => $validated['name'] ?? $method->name,
                'enabled' => $request->boolean('enabled', $method->enabled),
                'description' => $validated['description'] ?? $method->description,
                'priority' => $validated['priority'] ?? true,
            ]);

            if (!empty($validated['bank_accounts'])) {
                $method->paymentAttributes()->delete();

                foreach ($validated['bank_accounts'] as $index => $bank) {
                    foreach ($bank as $key => $value) {
                        $method->paymentAttributes()->create([
                            'key' => "bank_account_{$index}_{$key}",
                            'value' => $value,
                        ]);
                    }
                }
            }

            return back()->with('success', 'Direct Bank Transfer Method Updated Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function deleteAdminPaymentMethod(Request $request, $id)
    {
        try {
            $payment_method = PaymentMethod::find($id);

            if (!$payment_method) abort(404, 'No Payment Method Found');

            $payment_method->delete();

            return back()->with('success', 'Payment Method Deleted Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteSelectedPaymentMethods(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No payment methods selected for deletion.');
            }

            $payment_methods = PaymentMethod::whereIn('id', $ids)->get();

            foreach ($payment_methods as $method) {
                $method->delete();
            }

            return redirect()->back()->with('success', 'Selected payment_methods deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected payment_methods.");
        }
    }


    public function deleteAllPaymentMethods()
    {
        try {
            $payment_methods = PaymentMethod::all();

            foreach ($payment_methods as $method) {
                $method->delete();
            }

            return redirect()->back()->with('success', 'All payment methods deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all payment_methods.");
        }
    }
}
