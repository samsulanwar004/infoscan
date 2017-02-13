<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\TransactionService;

class TransactionJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $data;
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
        $datas = [
            'transaction_code' => $this->data->transactionCode,
            'member_code' => $this->data->memberCode,
            'transaction_type' => $this->data->transactionType,
            'snap_id' => $this->data->snapId,
        ];

        (new TransactionService($datas))->saveTransaction();
    }
}
