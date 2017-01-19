<?php

namespace App\Listeners;

class UserEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function onUserLogin($event) {
        $eventName = 'login';
        //$this->saveActivtyLog($event, $eventName);
    }

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event) {
        $eventName = 'logout';
        //$this->saveActivtyLog($event, $eventName);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Listeners\UserEventSubscriber@onUserLogin'
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'App\Listeners\UserEventSubscriber@onUserLogout'
        );
    }

    public function saveActivtyLog($event, $eventName) {

        $ua = new \App\UserActivities;
        $ua->action_in = $eventName;
        $ua->description = '';
        $ua->ip_address = $this->getIpAddress();
        $ua->user()->associate($event->user);
        $ua->save();
    }

    private function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

}