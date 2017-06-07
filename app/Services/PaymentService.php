<?php

namespace App\Services;

use App\Exchange;
use App\RedeemPoint;
use App\Jobs\MemberActionJob;

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

        $snaps = $snaps->filter(function ($value, $Key) {
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
            'minimum_point' => $exchange->minimum_point,
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

        if ($currentMemberPoint < $point) {
            throw new \Exception("Credit not enough!", 1);
        }

        $exchange = $this->getExchange();

        $exchangeCash = $exchange->cash_per_unit;
        $exchangePoint = $exchange->point_unit_count;
        $minimumPoint = $exchange->minimum_point;

        if ($point < $minimumPoint) {
            throw new \Exception("Credit must be greater than minimum point", 1);
        }

        if ($point < $exchangePoint) {
            throw new \Exception("Credit must be greater than exchange rate", 1);
        }

        $cashout = ($point / $exchangePoint) * $exchangeCash;

        $transaction = [
            'member_code' => $this->member->member_code,
            'point' => $point,
            'cashout' => $cashout,
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

        //build data for member history
        $content = [
            'type' => 'cashback',
            'title' => 'Cashback',
            'description' => 'Kamu telah menukarkan poin untuk cashback. Kami akan mengirim notifikasi setelah kami verifikasi.',
            'flag' => 1,
        ];

        $config = config('common.queue_list.member_action_log');
        $job = (new MemberActionJob($this->member->id, 'portalpoint', $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
        dispatch($job);

        return true;

    }

    public function transactionCredit($transaction)
    {
        $cashier = config('common.transaction.member.cashier');
        $cashierMoney = config('common.transaction.member.cashier_money');
        $data = [
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $transaction['member_code'],
                    'member_code_to' => $cashier,
                    'amount' => $transaction['point'],
                    'detail_type' => 'db',
                ],
                '1' => [
                    'member_code_from' => $transaction['member_code'],
                    'member_code_to' => $cashier,
                    'amount' => $transaction['point'],
                    'detail_type' => 'cr',
                ],
                '2' => [
                    'member_code_from' => $cashierMoney,
                    'member_code_to' => $transaction['member_code'],
                    'amount' => $transaction['cashout'],
                    'detail_type' => 'db',
                ],
                '3' => [
                    'member_code_from' => $cashierMoney,
                    'member_code_to' => $transaction['member_code'],
                    'amount' => $transaction['cashout'],
                    'detail_type' => 'cr',
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