<?php

namespace App\Services;

use App\Transaction;
use App\TransactionDetail;
use App\MemberActionLog;
use Carbon\Carbon;

class TransactionService
{

    /**
     * @var string
     */
    private $data;

    private $transactionCode;

    private $memberCode;

    private $transactionType;

    private $snapId;

    private $detailTransaction;

    private $currentRate;

    private $date;

    /**
     * @param array $data
     */

    public function __construct($data = null)
    {
        $this->date = Carbon::now('Asia/Jakarta');

        $this->transactionCode = isset($data['transaction_code']) ? $data['transaction_code'] : '';
        $this->memberCode = isset($data['member_code']) ? $data['member_code'] : '';
        $this->transactionType = isset($data['transaction_type']) ? $data['transaction_type'] : '';
        $this->snapId = isset($data['snap_id']) ? $data['snap_id'] : '';
        $this->currentRate = isset($data['current_rate']) ? $data['current_rate'] : '';
        $this->detailTransaction = isset($data['detail_transaction']) ? $data['detail_transaction'] : '';
    }

    public function getAllTransaction()
    {
    	return Transaction::orderBy('updated_at', 'DESC')
    		->paginate(50);
    }

    public function getTransactionDetailById($id)
    {
        return Transaction::with('transactionDetail')
                          ->where('id', '=', $id)
                          ->first();
    }

    public function getCreditMember($memberCode)
    {
        $cashier = config('common.transaction.member.cashier');

        $cr = \DB::table('transaction_detail')
                 ->where('member_code_to', '=', $memberCode)
                 ->where('member_code_from', '=', $cashier)
                 ->where('detail_type', '=', 'cr')
                 ->sum('amount');

        $db = \DB::table('transaction_detail')
                 ->where('member_code_from', '=', $memberCode)
                 ->where('member_code_to', '=', $cashier)
                 ->where('detail_type', '=', 'db')
                 ->sum('amount');

        $credit = $cr - $db;

        return $credit;
    }

    public function getCashCreditMember($memberCode)
    {
        $cashier = config('common.transaction.member.cashier_money');

        $cr = \DB::table('transaction_detail')
                 ->where('member_code_to', '=', $memberCode)
                 ->where('member_code_from', '=', $cashier)
                 ->where('detail_type', '=', 'cr')
                 ->sum('amount');

        $db = \DB::table('transaction_detail')
                 ->where('member_code_from', '=', $memberCode)
                 ->where('member_code_to', '=', $cashier)
                 ->where('detail_type', '=', 'db')
                 ->sum('amount');

        $credit = $cr - $db;

        return $credit;
    }

    public function saveTransaction()
    {
        $t = new Transaction;
        $t->transaction_code = $this->transactionCode;
        $t->member_code = $this->memberCode;
        $t->transaction_type = $this->transactionType;
        $t->snap_id = $this->snapId;

        $t->save();
    }

    public function savePoint()
    {
        $snapId = $this->snapId;
        $t = $this->getTransactionBySnapId($snapId);

        $t->current_cash_per_unit = $this->currentRate;
        $t->update();

        foreach ($this->detailTransaction as $data) {
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

    public function redeemPointToLuckyDraw($transaction, $datas)
    {
        $t = new Transaction;
        $t->transaction_code = strtolower(str_random(10));
        $t->member_code = $transaction['member_code'];
        $t->transaction_type = config('common.transaction.transaction_type.lottery');

        $t->save();

        foreach ($datas['detail_transaction'] as $data) {
            $td = new TransactionDetail;
            $td->member_code_from = $data['member_code_from'];
            $td->member_code_to = $data['member_code_to'];
            $td->amount = $data['amount'];
            $td->detail_type = $data['detail_type'];
            $td->transaction()->associate($t);
            $td->save();
        }

    }

    public function redeemPointToCash($transaction, $datas)
    {
        $t = new Transaction;
        $t->transaction_code = strtolower(str_random(10));
        $t->member_code = $transaction['member_code'];
        $t->transaction_type = config('common.transaction.transaction_type.redeem');

        $t->save();

        foreach ($datas['detail_transaction'] as $data) {
            $td = new TransactionDetail;
            $td->member_code_from = $data['member_code_from'];
            $td->member_code_to = $data['member_code_to'];
            $td->amount = $data['amount'];
            $td->detail_type = $data['detail_type'];
            $td->transaction()->associate($t);
            $td->save();
        }
    }

    public function getHistoryMember($memberId)
    {
        return MemberActionLog::where('member_id', $memberId)
                              ->orderBy('created_at', 'DESC')
                              ->get();
    }

    public function getHistoryTransaction($member)
    {
        $snapService = (new SnapService);
        $point = $this->getCreditMember($member->member_code);
        $snaps = $snapService->getSnapByMemberId($member->id);

        $end = $this->date->format('Y-m-d');
        $start = $this->date->subWeek()->format('Y-m-d');

        $historys = $snaps->filter(function ($value, $Key) use ($start, $end) {
            return $value->updated_at->format('Y-m-d') >= $start &&
                $value->updated_at->format('Y-m-d') <= $end;
        });

        $notif = [];
        foreach ($historys as $history) {
            $notif[] = [
                'title' => $snapService->getType($history->snap_type),
                'description' => $history->comment,
                'mode_type' => $history->mode_type,
                'thumbnail' => config('filesystems.s3url') . $history->files[0]->file_path,
                'status' => $history->status,
                'date' => $history->updated_at->toDateTimeString(),
            ];
        }
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
            'history' => $notif,
        ];

        return $data;
    }

    public function getNotification($member)
    {
        $notifications = $this->getHistoryMember($member->id);

        $notifications = $notifications->filter(function ($value, $Key) {
            return $value->group == 'notification' || $value->group == 'authentication';
        });

        $end = $this->date->format('Y-m-d');
        $start = $this->date->subWeek()->format('Y-m-d');

        $notifications = $notifications->filter(function ($value, $Key) use ($start, $end) {
            return $value->created_at->format('Y-m-d') >= $start &&
                $value->created_at->format('Y-m-d') <= $end;
        });

        $notif = [];
        foreach ($notifications as $notification) {
            $notif[] = [
                'type' => $notification->content['type'],
                'title' => $notification->content['title'],
                'description' => $notification->content['description'],
                'date' => $notification->created_at->toDateTimeString(),
            ];
        }

        return $notif;
    }

    public function savePointProcess()
    {
        $t = new Transaction;
        $t->transaction_code = $this->transactionCode;
        $t->member_code = $this->memberCode;
        $t->transaction_type = $this->transactionType;
        $t->current_cash_per_unit = $this->currentRate;

        $t->save();

        foreach ($this->detailTransaction as $data) {
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
}
