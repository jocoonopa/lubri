<?php

namespace App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Copy;

use App\Events\Flap\PIS_Goods\CopyToCometrust\CopyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResultAssembleEvent
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
     * @param  CopyEvent  $event
     * @return void
     */
    public function handle(CopyEvent $event)
    {
        //
    }
}
