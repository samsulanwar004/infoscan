<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\PointService;
use App\Services\TransactionService;

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
        $kasir = config('common.transaction.member.cashier');
        $member = config('common.transaction.member.user');
        $data = [
            'snap_id' => $this->data->id,
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $kasir,
                    'member_code_to' => $member,
                    'amount' => $point,
                    'detail_type' => 'db'
                ],
                '1' => [
                    'member_code_from' => $member,
                    'member_code_to' => $kasir,
                    'amount' => $point,
                    'detail_type' => 'cr'
                ],
            ],
        ];
        (new TransactionService($data))->savePoint();

    }
}
