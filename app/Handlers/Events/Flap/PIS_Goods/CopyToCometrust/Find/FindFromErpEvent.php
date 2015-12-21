<?php

namespace App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Find;

use App\Events\Event;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FindFromErpEvent
{
    /**
     * Handle the event.
     *
     * @param  FindEvent  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $codes = $event->getCodes();

        return $event->setGoodses(Processor::getArrayResult($this->getQuery($codes)));
    }

    public function getQuery(array $codes)
    {
        return Processor::table('PIS_Goods')
            ->whereIn('Code', $codes)
            ->where('Code', 'NOT LIKE', 'CT%')
        ;
    }
}
