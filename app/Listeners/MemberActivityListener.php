<?php

namespace App\Listeners;

use App\Events\MemberActivityEvent;
use App\Jobs\MemberLog;

class MemberActivityListener
{

    public function handle(MemberActivityEvent $event)
    {
        $job = (new MemberLog($event))->onQueue('memberLog');
        dispatch($job);
    }

}
