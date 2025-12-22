<?php

namespace App\Order\Controllers;

use App\Order\Models\Invoice;
use App\Order\Models\Payment;
use App\Order\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class PaymentController
{
    public function viewUserInvoiceListPage()
    {
        try {
            if (!auth()->check()) abort(403, 'Please log in to continue');
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

    public function viewUserInvoicePage($invoiceId)
    {
        try {
            if (!auth()->check()) abort(403, 'Please log in to continue');

            $invoice = Invoice::where('id', $invoiceId)
                ->first();

            if (!$invoice) abort(404, 'No invoice found');

            if ($invoice->order->user_id !== auth()->id()) {
                abort(403, 'You are not authorized to view this invoice');
            }

            return view('pages.user.dashboard.payment_invoice_detail', [
                'invoice' => $invoice->jsonResponse(['order', 'payment'])
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
                return $payment->jsonResponse(['order', 'invoice', 'payment_method']);
            });

            return view('pages.admin.dashboard.order.payment_list', [
                'payments' => $payments
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminPaymentDetailPage(Request $request, $payment_id)
    {
        try {
            $payment = Payment::findOrFail($payment_id);
            $payment = $payment->jsonResponse();

            $order_id = $payment['order_id'];
            $transactions = [];

            if ($order_id) {
                $transactions = Transaction::where('order_id', $order_id)->orderBy('id', 'desc')->get();
                $transactions = $transactions->map(fn($i) => $i->jsonResponse(['user', 'invoice', 'order']));
            }

            return view('pages.admin.dashboard.order.payment_detail', [
                'payment' => $payment,
                'transactions' => $transactions,
            ]);
        } catch (Exception $e) {
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
