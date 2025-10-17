<?php

namespace App\Payment\Controllers;

use App\Payment\Models\Invoice;
use Exception;
use Illuminate\Http\Request;

class InvoiceController
{

    public function viewAdminInvoiceListPage()
    {
        try {
            $invoices = Invoice::orderBy('id', 'desc')->paginate(20);

            $invoices->getCollection()->transform(function ($invoice) {
                return $invoice->jsonResponse(['order']);
            });
            return view('pages.admin.dashboard.payment.invoice_list', [
                'invoices' => $invoices
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
}
