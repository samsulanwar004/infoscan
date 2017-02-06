<?php

namespace App\Listeners;

use App\Events\TransactionEvent;
use App\Services\TransactionService;

class TransactionListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TransactionEvent  $event
     * @return void
     */
    public function handle(TransactionEvent $event)
    {
        $data = [
            'transaction_code' => $event->transaction_code,
            'member_code' => $event->member_code,
            'transaction_type' => $event->transaction_type,
            'snap_id' => $event->snap_id,
        ];
        (new TransactionService($data))->saveTransaction();
    }

}