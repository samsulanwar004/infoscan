<?php

namespace App\Listeners;

use App\Events\MemberActivityEvent;
use App\Jobs\MemberLog;

class MemberActivityListener
{

    public function handle(MemberActivityEvent $event)
    {
    	$config = config('common.queue_list.member_log');
        $job = (new MemberLog($event))->onQueue($config)->onConnection('sqs');
        dispatch($job);
    }

}
