<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class MemberActivityEvent
{
    use SerializesModels;

    public $member;

    public $action;

    public $description;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($member, $action, $description = '')
    {
        $this->member = $member;
        $this->action = $action;
        $this->description = $description;
    }

}
