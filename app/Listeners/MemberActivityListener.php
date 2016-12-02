<?php

namespace App\Listeners;

use App\Events\MemberActivityEvent;
use DB;

class MemberActivityListener
{
    const ACTIVITY_TABLE = 'member_activities';

    public function handle(MemberActivityEvent $event)
    {
        DB::table(self::ACTIVITY_TABLE)->insert([
            'member_code' => $event->member,
            'action_in' => $event->action,
            'description' => $event->description
        ]);
    }
}
