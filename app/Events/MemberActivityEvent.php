<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class MemberActivityEvent implements ShouldQueue
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
