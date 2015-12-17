<?php

namespace App\Handlers\Events\Flap\CCS_OrderIndex\PrefixEvent;

use App\Events\Flap\CCS_OrderIndex\PrefixEvent;
use App\Utility\Chinghwa\Flap\CCS_OrderIndex\PrefixHandler;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyOrderEvent
{
    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(PrefixEvent $prefixEvent)
    {
        $modifyOrderNos = with(new PrefixHandler)->execModifyOrderNos()->getModifyOrders();
        
        return (!empty($modifyOrderNos)) ? $prefixEvent->setModifyOrders($modifyOrderNos) : false;
    }
}
