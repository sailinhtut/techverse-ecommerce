<?php

namespace App\Payment\Controllers;

use App\Payment\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class TransactionController
{
    public function viewAdminTransactionListPage()
    {
        try {
            $transactions = Transaction::orderBy('id', 'desc')->paginate(20);

            $transactions->getCollection()->transform(function ($transaction) {
                return $transaction->jsonResponse(['user']);
            });
            return view('pages.admin.dashboard.payment.transaction_list', [
                'transactions' => $transactions
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteAdminTransaction(Request $request, $id)
    {
        try {
            $transaction = Transaction::find($id);
            if (!$transaction) abort(404, 'No transaction found');
            $transaction->delete();

            return redirect()->back()->with('success', 'Transaction is deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
