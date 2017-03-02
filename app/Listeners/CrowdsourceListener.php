<?php

namespace App\Listeners;

use App\Events\CrowdsourceEvent;
use App\Jobs\CrowdsourceLog;

class CrowdsourceListener
{

    public function handle(CrowdsourceEvent $event)
    {
    	$config = config('common.queue_list.crowdsource_log');
        $job = (new CrowdsourceLog($event))->onQueue($config)->onConnection('sqs');
        dispatch($job);
    }

}