<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\PointService;
use App\Services\TransactionService;
use App\Services\MemberService;

class CreditProcessJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $memberCode;

    private $point;

    private $cash;

    private $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($memberCode, $point, $cash, $type)
    {
        $this->memberCode = $memberCode;
        $this->point = $point;
        $this->cash = $cash;
        $this->type = $type;
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
        $cash = $this->cash;

        $transactionData = [
            'transaction_code' => strtolower(str_random(10)),
            'member_code' => $this->memberCode,
            'transaction_type' => $this->type,
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $cashier,
                    'member_code_to' => $this->memberCode,
                    'amount' => $point,
                    'detail_type' => 'db'
                ],
                '1' => [
                    'member_code_from' => $cashier,
                    'member_code_to' => $this->memberCode,
                    'amount' => $point,
                    'detail_type' => 'cr'
                ],
                '2' => [
                    'member_code_from' => $cashierMoney,
                    'member_code_to' => $this->memberCode,
                    'amount' => $cash,
                    'detail_type' => 'db'
                ],
                '3' => [
                    'member_code_from' => $cashierMoney,
                    'member_code_to' => $this->memberCode,
                    'amount' => $cash,
                    'detail_type' => 'cr'
                ],
            ],
        ];

        (new TransactionService($transactionData))->savePointProcess();

        $memberService = (new MemberService);
        $member = $memberService->getMemberByCode($this->memberCode);
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
    }
}
