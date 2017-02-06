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
    	$this->transaction_code = isset($data['transaction_code']) ? $data['transaction_code'] : '';
    	$this->member_code = isset($data['member_code']) ? $data['member_code'] : '';
    	$this->transaction_type = isset($data['transaction_type']) ? $data['transaction_type'] : '';
        $this->snap_id = isset($data['snap_id']) ? $data['snap_id'] : '';
    	$this->detail_transaction = isset($data['detail_transaction']) ? $data['detail_transaction'] : '';
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

    public function getCreditMember($member_code)
	{
        //config transaction
        $member = config('common.transaction.member.snap');

		$cr = \DB::table('transactions')
            ->join('transaction_detail', 'transactions.id', '=', 'transaction_detail.transaction_id')
            ->where('member_code', '=', $member_code)
            ->where('member_code_from', '=', $member)
            ->where('detail_type', '=', 'CR')
            ->sum('amount');

        $db = \DB::table('transactions')
            ->join('transaction_detail', 'transactions.id', '=', 'transaction_detail.transaction_id')
            ->where('member_code', '=', $member_code)
            ->where('member_code_from', '=', $member)
            ->where('detail_type', '=', 'DB')
            ->sum('amount');

        $credit = $cr - $db;

        return $credit;
	}

    public function saveTransaction()
    {
        $t = new Transaction;
        $t->transaction_code = $this->transaction_code;
        $t->member_code = $this->member_code;
        $t->transaction_type = $this->transaction_type;
        $t->snap_id = $this->snap_id;

        $t->save();
    }

    public function savePoint()
    {
        $snapId = $this->snap_id;
        $t = $this->getTransactionBySnapId($snapId);
        foreach($this->detail_transaction as $data) {
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

    public function getTransactionBySnapId($snapId)
    {
        return Transaction::where('snap_id', $snapId)->first();
    }
}
