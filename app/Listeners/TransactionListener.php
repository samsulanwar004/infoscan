<?php

namespace App\Listeners;

use App\Events\TransactionEvent;
use App\Jobs\TransactionJob;

class TransactionListener
{

    /**
     * Handle the event.
     *
     * @param  TransactionEvent  $event
     * @return void
     */
    public function handle(TransactionEvent $event)
    {
        $job = (new TransactionJob($event))->onQueue('transactionProcess');
        dispatch($job);
    }

}