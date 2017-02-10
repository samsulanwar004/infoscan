<?php

namespace App\Listeners;

use App\Events\TransactionEvent;
use App\Services\TransactionService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransactionListener implements ShouldQueue
{

    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  TransactionEvent  $event
     * @return void
     */
    public function handle(TransactionEvent $event)
    {
        $data = [
            'transaction_code' => $event->transactionCode,
            'member_code' => $event->memberCode,
            'transaction_type' => $event->transactionType,
            'snap_id' => $event->snapId,
        ];
        (new TransactionService($data))->saveTransaction();
    }

}