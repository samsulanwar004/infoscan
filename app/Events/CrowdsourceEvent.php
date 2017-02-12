<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class CrowdsourceEvent
{
    use SerializesModels;

    public $userId;

    public $action;

    public $description;

    /**
     * Create a new event instance.
     *
     * @param  int  $userId
     * @param  string  $action
     * @param  string  $description
     * @return void
     */
    public function __construct($userId, $action, $description = null)
    {
        $this->userId = $userId;
        $this->action = $action;
        $this->description = $description;
    }
}