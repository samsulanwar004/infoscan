<?php

namespace App\Services;

use App\Exchange;
use App\RedeemPoint;

class PaymentService 
{

	/**
     * @var string
     */
    private $member;

	public function __construct()
	{
		$this->member = auth('api')->user();
	}

	public function getPaymentPortal()
	{
		$exchange = $this->getExchange();

		$point = (new TransactionService)->getCreditMember($this->member->member_code);

        $snaps = (new SnapService)->getSnapByMemberCode($this->member->member_code);

        $snaps = $snaps->filter(function($value, $Key) {
            return $value->approved_by == null && $value->reject_by == null;
        });

        $estimated = [];
        foreach ($snaps as $snap) {
            $estimated[] = $snap->estimated_point;
        }

        $estimated = collect($estimated)->sum();

        $data = [
	        'current_point' => $point,
	        'estimated_point' => $estimated,
	        'point_unit_count' => $exchange->point_unit_count,
	        'cash_per_unit' => $exchange->cash_per_unit,
       	];

		return $data;
	}

	public function getExchange()
	{
		return Exchange::orderBy('created_at', 'DESC')->first();
	}

	public function redeemPointToCash($request)
	{
		$point = $request->input('point');
		$bankAccount = $request->input('bank_account');
		$accountNumber = $request->input('account_number');
		$currentMemberPoint = (new TransactionService)->getCreditMember($this->member->member_code);

		if ($currentMemberPoint < $point)
		{
			throw new \Exception("Credit not enough!", 1);			
		}

		$exchange = $this->getExchange();

		$exchangeCash = $exchange->cash_per_unit;
		$exchangePoint = $exchange->point_unit_count;

		if ($exchangePoint > $point) {
			throw new \Exception("Credit must be greater than exchange rate", 1);
		}

		$cashout = ($point / $exchangePoint) * $exchangeCash;

		$transaction = [
			'member_code' => $this->member->member_code,
			'point' => $point,
		];	

		$data = [
			'point' => $point,
			'cashout' => $cashout,
			'bank_account' => $bankAccount,
			'account_number' => $accountNumber,
		];	

		//save to redeem point table
		$this->saveToRedeemPoint($data, $this->member);

		//credit point to member
		$this->transactionCredit($transaction);

		return true;

	}

	public function transactionCredit($transaction)
	{
		$kasir = config('common.transaction.member.cashier');
        $member = config('common.transaction.member.user');
        $data = [
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $kasir,
                    'member_code_to' => $member,
                    'amount' => $transaction['point'],
                    'detail_type' => 'cr'
                ],
                '1' => [
                    'member_code_from' => $member,
                    'member_code_to' => $kasir,
                    'amount' => $transaction['point'],
                    'detail_type' => 'db'
                ],
            ],
        ];

        (new TransactionService())->redeemPointToCash($transaction, $data);
	}

	public function saveToRedeemPoint($data, $member)
	{
		$redeem = new RedeemPoint;
		$redeem->point = $data['point'];
		$redeem->cashout = $data['cashout'];
		$redeem->bank_account = $data['bank_account'];
		$redeem->account_number = $data['account_number'];

		$redeem->member()->associate($member);

		$redeem->save();
	}

	public function getListPaymentPortal()
	{
		return RedeemPoint::with('member')
			->orderBy('created_at', 'DESC')
			->paginate(50);
	}

	public function getPaymentPortalById($id)
	{
		return RedeemPoint::with('member')
			->where('id', $id)
			->first();
	}

	public function saveConfirmation($request, $id)
	{
		$payment = $this->getPaymentPortalById($id);
		$payment->approved_by = auth('web')->user()->id;
		$payment->update();
	}
}