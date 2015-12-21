<?php

namespace App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Find;

use App\Events\Event;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckIfCTExistEvent
{
    const COMETRUST_PREFIX = 'CT';

    /**
     * Handle the event.
     *
     * @param  FindEvent  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $goodses = $event->getGoodses();
        $ctCodes = $this->getExistCtCodes($event);

        array_walk($goodses, function (&$goods) use ($ctCodes) {
            $ctCodeItShouldBe = self::COMETRUST_PREFIX . array_get($goods, 'Code');

            $goods['ctCode'] = (false !== (array_search($ctCodeItShouldBe, $ctCodes)))
                ? $ctCodeItShouldBe : false;
        });

        return $event->setGoodses($goodses);
    }

    protected function getExistCtCodes(Event $event)
    {
        return array_pluck(Processor::getArrayResult($this->getQuery($this->genCtPrefixCodes($event))), 'Code');
    }

    protected function getQuery(array $ctPrefixCodes)
    {
        return Processor::table('PIS_Goods')
            ->whereIn('Code', $ctPrefixCodes)
        ;
    }

    protected function genCtPrefixCodes(Event $event)
    {
        return preg_filter('/^/', self::COMETRUST_PREFIX, array_pluck($event->getGoodses(), 'Code'));
    }
}
