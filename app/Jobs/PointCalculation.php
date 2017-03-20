<?php

namespace App\Jobs;

use App\Services\NotificationService;
use App\Services\PointService;
use App\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PointCalculation implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $point = (new PointService)->calculateApprovePoint($this->data);
        $cashier = config('common.transaction.member.cashier');
        //$member = config('common.transaction.member.user');

        $member = $this->data->member->member_code;
        $transactionData = [
            'snap_id' => $this->data->id,
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $cashier,
                    'member_code_to' => $member,
                    'amount' => $point,
                    'detail_type' => 'db'
                ],
                '1' => [
                    'member_code_from' => $cashier,
                    'member_code_to' => $member,
                    'amount' => $point,
                    'detail_type' => 'cr'
                ],
            ],
        ];

        (new TransactionService($transactionData))->savePoint();

        $memberId = $this->data->member->id;
        $this->sendNotification($point, $memberId);
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
}
