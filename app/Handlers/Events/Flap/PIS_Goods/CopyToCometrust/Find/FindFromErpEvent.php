<?php

namespace App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Find;

use App\Events\Flap\PIS_Goods\CopyToCometrust\FindEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FindFromErpEvent
{
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
     * @param  FindEvent  $event
     * @return void
     */
    public function handle(FindEvent $event)
    {
        //
    }
}
