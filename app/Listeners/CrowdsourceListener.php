<?php

namespace App\Listeners;

use App\Events\CrowdsourceEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\CrowdsourceActivity;

class CrowdsourceListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CrowdsourceEvent $event)
    {
        $ca = new CrowdsourceActivity;
        $ca->action_in = $event->action;
        $ca->description = $event->description;
        $ca->user()->associate($event->userId);
        $ca->save();
    }

}