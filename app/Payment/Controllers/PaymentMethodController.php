<?php

namespace App\Payment\Controllers;

use App\Payment\Models\PaymentMethod;
use Exception;
use Illuminate\Http\Request;

class PaymentMethodController
{
    public function viewAdminPaymentMethodListPage()
    {
        try {
            $paymentmethods = PaymentMethod::orderBy('id', 'desc')->paginate(20);

            $paymentmethods->getCollection()->transform(function ($payment) {
                return $payment->jsonResponse(['paymentAttributes']);
            });

            // dd($paymentmethods);

            return view('pages.admin.dashboard.payment.payment_method_list', [
                'paymentmethods' => $paymentmethods
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
                'cod.instruction' => 'nullable|string|max:1000',
            ]);

            $method = PaymentMethod::create([
                'name' => $validated['name'],
                'code' => 'cod',
                'type' => 'manual',
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
                'enabled' => $request->boolean('enabled', true),
                'description' => $validated['description'] ?? null,
            ]);

            // foreach ($validated['bank_accounts'] as $bank) {
            //     foreach ($bank as $key => $value) {
            //         $method->paymentAttributes()->create([
            //             'key' => $key,
            //             'value' => $value,
            //         ]);
            //     }
            // }

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
                'cod.instruction' => 'nullable|string|max:1000',
            ]);

            $method = PaymentMethod::find($id);

            if (!$method) abort(404, 'No COD Payment Found');


            $method->update([
                'name' => $validated['name'] ?? $method->name,
                'enabled' => $request->boolean('enabled', $method->enabled),
                'description' => $validated['description'] ?? $method->description,
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
}
