<?php

namespace App\Order\Controllers;

use App\Order\Models\Invoice;
use App\Order\Models\Payment;
use App\Order\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class InvoiceController
{

    public function viewAdminInvoiceListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = Invoice::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('order_id', 'like', "%{$search}%")
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

            $invoices = $query->paginate($perPage);
            $invoices->appends(request()->query());

            $invoices->getCollection()->transform(function ($invoice) {
                return $invoice->jsonResponse(['order']);
            });
            return view('pages.admin.dashboard.order.invoice_list', [
                'invoices' => $invoices
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminInvoiceDetailPage(Request $request, $invoice_id)
    {
        try {
            $invoice = Invoice::findOrFail($invoice_id);
            $invoice = $invoice->jsonResponse(['order']);

            $order_id = $invoice['order_id'];

            $payments = Payment::where('order_id', $order_id)->orderBy('id', 'desc')->get();
            $payments = $payments->map(fn($i) => $i->jsonResponse(['invoice', 'order']));

            $transactions = Transaction::where('order_id', $order_id)->orderBy('id', 'desc')->get();
            $transactions = $transactions->map(fn($i) => $i->jsonResponse(['user', 'invoice', 'order']));

            return view('pages.admin.dashboard.order.invoice_detail', [
                'invoice' => $invoice,
                'payments' => $payments,
                'transactions' => $transactions,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteAdminInvoice(Request $request, $id)
    {
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) abort(404, 'No invoice found');
            $invoice->delete();

            return redirect()->back()->with('success', 'Invoice is deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteSelectedInvoices(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No invoices selected for deletion.');
            }

            $invoices = Invoice::whereIn('id', $ids)->get();

            foreach ($invoices as $invoice) {
                $invoice->delete();
            }

            return redirect()->back()->with('success', 'Selected invoices deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected invoices.");
        }
    }

    public function deleteAllInvoices()
    {
        try {
            $invoices = Invoice::all();

            foreach ($invoices as $invoice) {
                $invoice->delete();
            }

            return redirect()->back()->with('success', 'All invoices deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all invoices.");
        }
    }
}
