<?php

namespace App\Subscribers;

use App\Events\SomeTriggerEvent;

class SomeTriggerEventSubscriber {

    /**
     * Handle user login events.
     */
    public function onTrigger1($event)
    {
        echo 'T1';
    }

    /**
     * Handle user logout events.
     */
    public function onTrigger2($event)
    {
        echo 'T2';
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen('App\Events\SomeTriggerEvent', 'App\Subscribers\SomeTriggerEventSubscriber@onTrigger1');

        $events->listen('App\Events\SomeTriggerEvent', 'App\Subscribers\SomeTriggerEventSubscriber@onTrigger2');
    }

}