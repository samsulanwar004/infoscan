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
        $minimum = $this->getMinimumCash();
        // get current point
        $point = (new TransactionService)->getCreditMember($this->member->member_code);
        // get current cash
        $cash = (new TransactionService)->getCashCreditMember($this->member->member_code);

        // $snaps = (new SnapService)->getSnapByMemberCode($this->member->member_code);

        // $snaps = $snaps->filter(function($value, $Key) {
        //     return $value->approved_by == null && $value->reject_by == null;
        // });

        // $estimated = [];
        // foreach ($snaps as $snap) {
        //     $estimated[] = $snap->estimated_point;
        // }

        // $estimated = collect($estimated)->sum();

        $data = [
            'current_cash' => $cash,
            'current_point' => $point,
            // 'estimated_point' => $estimated,
            // 'point_unit_count' => $exchange->point_unit_count,
            // 'cash_per_unit' => $exchange->cash_per_unit,
            'minimum_cash' => $minimum,
        ];

        return $data;
    }

    public function getMinimumCash()
    {
        $rate = Exchange::where('minimum_point', '>', 0)
            ->orderBy('id', 'DESC')
            ->first();

        return $rate ? $rate->minimum_point : 0;
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

    public function redeemCashout($request)
    {
        $memberCode = $this->member->member_code;

        $name = $request->input('name');
        $cashback = $request->input('cashback');
        $bankAccount = $request->input('bank_account');
        $accountNumber = $request->input('account_number');

        $point = $this->getDeductPoint($memberCode, $cashback);

        $transaction = [
            'member_code' => $this->member->member_code,
            'point' => $point,
            'cashout' => $cashback,
        ];

        $data = [
            'name' => $name,
            'point' => $point,
            'cashout' => $cashback,
            'bank_account' => $bankAccount,
            'account_number' => $accountNumber,
        ];

        //save to redeem point table
        $redeemPoint = $this->saveToRedeemPoint($data, $this->member);

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

        $currentPoint = (new TransactionService)->getCreditMember($memberCode);
        $currentCash = (new TransactionService)->getCashCreditMember($memberCode);

        $redeemPoint->current_point = $currentPoint;
        $redeemPoint->current_cash = $currentCash;
        $redeemPoint->update();

        $this->member->temporary_point = $currentPoint;
        $this->member->temporary_cash = $currentCash;
        $this->member->update();

        return true;
    }

    public function getDeductPoint($memberCode, $cash)
    {
        $exchange = $this->getExchange();

        $minimumCash = $exchange->minimum_point;

        if ($cash < $minimumCash) {
            throw new \Exception("Credit must be greater than minimum cash", 1);
        }

        $currentMemberPoint = (new TransactionService)->getCreditMember($memberCode);
        $currentMemberCash = (new TransactionService)->getCashCreditMember($memberCode);

        $exitRate = $currentMemberCash / $currentMemberPoint;

        $point = $cash / $exitRate;

        if ($currentMemberCash < $cash) {
            throw new \Exception("Credit Cash not enough!", 1);
        }

        if ($currentMemberPoint < $point) {
            throw new \Exception("Credit Point not enough!", 1);
        }

        return round($point);
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
                    'member_code_from' => $transaction['member_code'],
                    'member_code_to' => $cashierMoney,
                    'amount' => $transaction['cashout'],
                    'detail_type' => 'db',
                ],
                '3' => [
                    'member_code_from' => $transaction['member_code'],
                    'member_code_to' => $cashierMoney,
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
        $redeem->name = $data['name'];
        $redeem->bank_account = $data['bank_account'];
        $redeem->account_number = $data['account_number'];
        $redeem->status = 'approved';

        $redeem->member()->associate($member);

        $redeem->save();

        return $redeem;
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

    public function getExportPaymentToCsv($data)
    {
        $results = RedeemPoint::whereDate('created_at', '>=', $data['start_date'])
                              ->whereDate('created_at', '<=', $data['end_date'])
                              ->orderBy('created_at', 'DESC')
                              ->paginate(200);

        if ($data['type'] == 'new') {
            $filename = strtolower(str_random(10)) . '.csv';
            $title = 'No,Point Redeem,Cashout,Current Point,Current Money,Username,Email,Name,Bank Account,Account Number,Status,Date';
            \Storage::disk('csv')->put($filename, $title);
            $no = 1;
            foreach ($results as $row) {
                $baris = $no . ',' . number_format($row['point'],0,0,'.') . ',' . number_format($row['cashout'],0,0,'.')
                . ',' . number_format($row['current_point'],0,0,'.'). ',' . number_format($row['current_cash'],0,0,'.')
                . ',' . $row['member']->name . ','. $row['member']->email . ',' . $row['name']
                . ',' . $row['bank_account'] . ',' . $row['account_number']
                . ',' . $row['status'] . ',' . $row['created_at'];
                \Storage::disk('csv')->append($filename, $baris);
                $no++;
            }

        } else {
            if ($data['type'] == 'next') {
                $filename = $data['filename'];
                $no = $data['no'];
                foreach ($results as $row) {
                    $baris = $no . ',' . number_format($row['point'],0,0,'.') . ',' . number_format($row['cashout'],0,0,'.')
                    . ',' . number_format($row['current_point'],0,0,'.'). ',' . number_format($row['current_cash'],0,0,'.')
                    . ',' . $row['member']->name . ','. $row['member']->email . ',' . $row['name']
                    . ',' . $row['bank_account'] . ',' . $row['account_number']
                    . ',' . $row['status'] . ',' . $row['created_at'];
                    \Storage::disk('csv')->append($filename, $baris);
                    $no++;
                }
            }
        }

        $lastPage = $results->lastPage();
        $params = [
            'type_request' => ($lastPage == $data['page'] || count($results) == 0) ? 'download' : 'next',
            'filename' => $filename,
            'page' => $data['page'] + 1,
            'no' => $no,
            'last' => $lastPage,
        ];

        return $params;
    }
}