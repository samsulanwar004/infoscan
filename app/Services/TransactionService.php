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

    private $date;

    /**
     *
     * @param array $data
     */

    public function __construct($data = null)
    {
        $this->date = Carbon::now('Asia/Jakarta');

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
        $cashier = config('common.transaction.member.cashier');

		$cr = \DB::table('transaction_detail')
            ->where('member_code_to', '=', $member_code)
            ->where('member_code_from', '=', $cashier)
            ->where('detail_type', '=', 'cr')
            ->sum('amount');

        $db = \DB::table('transaction_detail')
            ->where('member_code_from', '=', $member_code)
            ->where('member_code_to', '=', $cashier)
            ->where('detail_type', '=', 'db')
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

    public function redeemPointToLuckyDraw($transaction, $datas)
    {
        $t = new Transaction;
        $t->transaction_code = strtolower(str_random(10));
        $t->member_code = $transaction['member_code'];
        $t->transaction_type = config('common.transaction.transaction_type.lottery');

        $t->save();

        foreach($datas['detail_transaction'] as $data) {
            $td = new TransactionDetail;
            $td->member_code_from = $data['member_code_from'];
            $td->member_code_to = $data['member_code_to'];
            $td->amount = $data['amount'];
            $td->detail_type = $data['detail_type'];
            $td->transaction()->associate($t);
            $td->save();
        }

    }

    public function debitNewMember($transaction, $datas)
    {
        $t = new Transaction;
        $t->transaction_code = strtolower(str_random(10));
        $t->member_code = $transaction['member_code'];
        $t->transaction_type = config('common.transaction.transaction_type.register');

        $t->save();

        foreach($datas['detail_transaction'] as $data) {
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

        foreach($datas['detail_transaction'] as $data) {
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

        $snaps = $snaps->filter(function($value, $Key) use ($start, $end) {
            return $value->updated_at->format('Y-m-d') >= $start &&
                    $value->updated_at->format('Y-m-d') <= $end;
        }); 

        $notif = [];
        foreach ($snaps as $snap) {
            $notif[] = [
                'title' => $snapService->getType($snap->snap_type),
                'description' => $snap->comment,
                'mode_type' => $snap->mode_type,
                'thumbnail' => config('filesystems.s3url').$snap->files[0]->file_path,
                'status' => $snap->status,
                'date'  => $snap->updated_at->toDateTimeString(),
            ];
        }
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
            'history' => $notif,
        ];

        return $data;
    }

    public function getNotification($member)
    {
        $notifications = $this->getHistoryMember($member->id);

        $notifications = $notifications->filter(function($value, $Key) {
            return $value->group == 'notification' || $value->group == 'authentication';
        });

        $end = $this->date->format('Y-m-d');
        $start = $this->date->subWeek()->format('Y-m-d');

        $notifications = $notifications->filter(function($value, $Key) use ($start, $end) {
            return $value->created_at->format('Y-m-d') >= $start &&
                    $value->created_at->format('Y-m-d') <= $end;
        });               

        $notif = [];
        foreach ($notifications as $notification) {
            $notif[] = [
                'type' => $notification->content['type'],
                'title' => $notification->content['title'],
                'description' => $notification->content['description'],
                'date'  => $notification->created_at->toDateTimeString(),
            ];
        }

        return $notif;
    }
}
