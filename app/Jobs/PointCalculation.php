<?php

namespace App\Jobs;

use App\Services\NotificationService;
use App\Services\PointService;
use App\Services\TransactionService;
use App\Services\MemberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PointCalculation implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $point;
    protected $promo;

    private $isSendNotification = true;

    /**
     * Create a new job instance.
     *
     * @param $data
     * @param $point
     * @param $promo
     */
    public function __construct($data = null, $point = null, $promo = null)
    {
        $this->data = $data;
        $this->point = $point;
        $this->promo = $promo;
    }

    public function initialize($data, $point, $promo = null)
    {
        $this->data = $data;
        $this->point = $point;
        $this->promo = $promo;

        return $this;
    }

    /**
     * @param bool $send
     *
     * @return $this
     */
    public function setIsSendNotification($send)
    {
        $this->isSendNotification = $send;

        return $this;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $point = $this->point;
        $cashier = config('common.transaction.member.cashier');
        $cashierMoney = config('common.transaction.member.cashier_money');
        $type = config('common.transaction.transaction_type.snaps');

        $city = $this->data->outlet_city;

        $cityRate = (new PointService)->getCurrencyRateByCity($city);
        if ($cityRate) {
            $exchange = $cityRate;
        } else {
            $exchange = (new PointService)->getCurrencyRate();
        }

        $rate = isset($exchange) ? $exchange->cash_per_unit : 0;
        $cash = $rate * $point;

        $snap = $this->getSnapById($this->data->id);
        $snap->status = 'approve';
        $snap->fixed_point = $point;
        $snap->current_exchange_rate = $rate;
        $snap->current_total_money = $cash;
        $snap->update();

        $member = $this->data->member->member_code;
        $transactionData = [
            'transaction_code' => strtolower(str_random(10)),
            'member_code' => $member,
            'transaction_type' => $type,
            'snap_id' => $this->data->id,
            'current_rate' => $rate,
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $cashier,
                    'member_code_to' => $member,
                    'amount' => $point,
                    'detail_type' => 'db',
                ],
                '1' => [
                    'member_code_from' => $cashier,
                    'member_code_to' => $member,
                    'amount' => $point,
                    'detail_type' => 'cr',
                ],
                '2' => [
                    'member_code_from' => $cashierMoney,
                    'member_code_to' => $member,
                    'amount' => $cash,
                    'detail_type' => 'db',
                ],
                '3' => [
                    'member_code_from' => $cashierMoney,
                    'member_code_to' => $member,
                    'amount' => $cash,
                    'detail_type' => 'cr',
                ],
            ],
        ];

        (new TransactionService($transactionData))->savePointProcess();

        $memberService = (new MemberService);
        $member = $memberService->getMemberByCode($member);
        $pointMember = (new TransactionService)->getCreditMember($member->member_code);
        $memberService = $memberService->getLevelByMemberId($member->id);
        $levelId = $memberService['level_id'];
        $level = (new PointService)->getLevel($levelId);
        $levelArray = explode(' ', $level->name);

        //get latest member point
        $score = $memberService['latest_point'];

        if ($member->leaderboard_score != $score) {
            $member->leaderboard_score = $score;

            $member->update();
        }

        if ($member->temporary_point != $pointMember || $member->temporary_level != $levelArray[1]) {
            $member->temporary_point = $pointMember;
            $member->temporary_level = $levelArray[1];

            $member->update();
        }

        if (true === $this->isSendNotification) {
            $memberId = $this->data->member->id;
            $this->sendNotification($point, $memberId);
        }
    }

    private function sendNotification($point, $memberId)
    {
        $message = config('common.notification_messages.snaps.success');
        $sendMessage = sprintf("$message", (string)$point);

        (new NotificationService($sendMessage))->setUser($memberId)
                                               ->setData([
                                                   'action' => 'history',
                                               ])->send();
    }

    private function getSnapById($id)
    {
        return \App\Snap::where('id', $id)->first();
    }
}
