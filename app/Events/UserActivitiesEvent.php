<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UserActivitiesEvent
{
    use SerializesModels;

    public $userId;

    public $action;

    public $description;

    public $ipAddress;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct($userId, $action, $description = '', $ipAddress)
    {
        $this->user_id = $userId;
        $this->action = $action;
        $this->description = $description;
        $this->ip_address = $ipAddress;
    }
}