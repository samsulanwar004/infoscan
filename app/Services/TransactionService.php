<?php

namespace App\Services;

use App\Transaction;
use App\TransactionDetail;

class TransactionService
{

    /**
     * @var string
     */
    private $data;

    /**
     *
     * @param array $data
     */

    public function __construct($data = null)
    {
    	$this->transactionCode = strtolower(str_random(10));
    	$this->memberCode = $data['member_code'];
    	$this->transactionType = $data['transaction_type'];
    	$this->detailTransaction = $data['transaction_detail'];
    }

    public function create()
    {
    	//TODO : check member
    	$t = new Transaction;
    	$t->transaction_code = $this->transactionCode;
    	$t->member_code = $this->memberCode;
    	$t->transaction_type = $this->transactionType;
    	$t->save();

    	foreach($this->detailTransaction as $data) {
    		$td = new TransactionDetail;
	    	$td->member_code_from = $data['member_code_from'];
	    	$td->member_code_to = $data['member_code_to'];
	    	$td->amount = $data['amount'];
	    	$td->detail_type = $data['detail_type'];
	    	$td->transaction()->associate($t);
	    	$td->save();
	    }

    	return true;
    }

    public function getAllTransaction()
    {
    	return Transaction::orderBy('id', 'DESC')
    		->paginate(50);
    }

    public function getTransactionDetailById($id)
    {
    	return Transaction::with('transactionDetail')
    		->where('id', '=', $id)
    		->first();
    }

}
