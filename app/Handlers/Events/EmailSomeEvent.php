<?php

namespace App\Handlers\Events;

use App\Events\SomeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailSomeEvent
{
    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(SomeEvent $event)
    {
        return $event->show($this);
    }
}
