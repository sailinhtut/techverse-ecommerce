<?php

namespace App\Payment\Controllers;

use App\Inventory\Services\CouponService;
use App\Order\Models\Order;
use App\Order\Services\OrderService;
use App\Payment\Models\Invoice;
use App\Payment\Models\Payment;
use App\Payment\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController
{
    public function viewUserInvoiceListPage()
    {
        try {
            if (!auth()->check()) abort(403, 'Unauthenticated');
            $invoices = auth()->user()->invoices()->latest()
                ->paginate(10);

            $invoices->getCollection()->transform(function ($invoice) {
                return $invoice->jsonResponse(['order']);
            });

            return view('pages.user.dashboard.payment_invoice', [
                'invoices' => $invoices
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminPaymentListPage(Request $request)
    {
        try {

            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = Payment::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%");
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

            $payments = $query->paginate($perPage);
            $payments->appends(request()->query());

            $payments->getCollection()->transform(function ($payment) {
                return $payment->jsonResponse(['invoice', 'payment_method']);
            });

            return view('pages.admin.dashboard.payment.payment_list', [
                'payments' => $payments
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function completePayment(Request $request, $orderId)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);

            $today = now();
            $order_number = sprintf('ORD-%d-%d-%d-%04d', $today->year, $today->month, $today->day, (Order::max('id') ?? 0) + 1);

            $invoice = Invoice::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'invoice_number' => sprintf('INV-%s', $order->order_number),
                    'subtotal' => $order->subtotal,
                    'discount_total' => $order->discount_total,
                    'tax_total' => $order->tax_total,
                    'shipping_total' => $order->shipping_total,
                    'grand_total' => $order->grand_total,
                    'status' => 'paid',
                    'issued_at' => now(),
                ]
            );

            if ($invoice->wasRecentlyCreated === false) {
                $invoice->update(['status' => 'paid']);
            }

            $payment = Payment::firstOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'payment_method_id' => $order->payment_method_id,
                    'transaction_id' => Str::uuid(),
                    'amount' => $invoice->grand_total,
                    'details' => json_encode([
                        'method' =>  'COD',
                        'completed_at' => now()->toDateTimeString(),
                    ]),
                ]
            );

            $transaction = Transaction::firstOrCreate(
                ['payment_id' => $payment->id],
                [
                    'user_id' => $order->user_id,
                    'reference' => $payment->transaction_id,
                    'type' => 'credit',
                    'status' => 'completed',
                    'amount' => $payment->amount,
                ]
            );

            if ($transaction->wasRecentlyCreated === false) {
                $transaction->update(['status' => 'completed']);
            }



            DB::commit();

            return redirect()->back()->with('success', 'Payment completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return handleErrors($e);
        }
    }

    public function deleteAdminPayment(Request $request, $id)
    {
        try {
            $payment = Payment::find($id);
            if (!$payment) abort(404, 'No payment found');
            $payment->delete();

            return redirect()->back()->with('success', 'Payment is deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteSelectedPayments(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No payments selected for deletion.');
            }

            $payments = Payment::whereIn('id', $ids)->get();

            foreach ($payments as $payment) {
                $payment->delete();
            }

            return redirect()->back()->with('success', 'Selected payments deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected payments.");
        }
    }


    public function deleteAllPayments()
    {
        try {
            $payments = Payment::all();

            foreach ($payments as $payment) {
                $payment->delete();
            }

            return redirect()->back()->with('success', 'All payments deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all payments.");
        }
    }
}
