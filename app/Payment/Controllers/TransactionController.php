<?php

namespace App\Payment\Controllers;

use App\Payment\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class TransactionController
{
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
