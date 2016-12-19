<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\TransactionService;

class HistoryController extends AdminController
{
    public function transactions()
    {
    	$transactions = (new TransactionService)->getAllTransaction();

    	return view('transactions.index_transactions', compact('transactions'));
    }

    public function showTransaction($id)
    {
    	$transaction = (new TransactionService)->getTransactionDetailById($id);

    	return view('transactions.show_transactions', compact('transaction'));
    }
}
