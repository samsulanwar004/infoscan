<?php

namespace App\Listeners;

use App\Events\UserActivitiesEvent;
use DB;

class UserActivitiesListener
{

    const ACTIVITY_TABLE = 'users_activities';

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
     * @param  OrderShipped  $event
     * @return void
     */
    public function handle(UserActivitiesEvent $event)
    {
        DB::table(self::ACTIVITY_TABLE)->insert([
            'user_id' => $event->user_id,
            'action_in' => $event->action,
            'description' => $event->description,
            'ip_address' => $event->ip_address,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }

}