<?php

namespace App\Listeners;

use App\Events\CrowdsourceEvent;
use App\Jobs\CrowdsourceLog;

class CrowdsourceListener
{

    public function handle(CrowdsourceEvent $event)
    {
        $job = (new CrowdsourceLog($event))->onQueue('crowdsourceLog');
        dispatch($job);
    }

}