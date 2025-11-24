<?php

namespace App\Payment\Controllers;

use App\Payment\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class TransactionController
{
    public function viewAdminTransactionListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = Transaction::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('id', 'like', "%{$search}%");
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

            $transactions = $query->paginate($perPage);
            $transactions->appends(request()->query());

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


    public function deleteSelectedTransactions(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No transactions selected for deletion.');
            }

            $transactions = Transaction::whereIn('id', $ids)->get();

            foreach ($transactions as $transaction) {
                $transaction->delete();
            }

            return redirect()->back()->with('success', 'Selected transactions deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected transactions.");
        }
    }

    public function deleteAllTransactions()
    {
        try {
            $transactions = Transaction::all();

            foreach ($transactions as $transaction) {
                $transaction->delete();
            }

            return redirect()->back()->with('success', 'All transactions deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all transactions.");
        }
    }
}
