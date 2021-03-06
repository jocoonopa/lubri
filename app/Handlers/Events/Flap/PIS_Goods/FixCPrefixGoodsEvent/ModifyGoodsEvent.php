<?php

namespace App\Handlers\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent;

use App\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent;
use App\Utility\Chinghwa\Helper\Flap\PIS_Goods\FixCPrefixGoods\DataHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyGoodsEvent
{
    /**
     * Handle the event.
     *
     * @param  FixCPrefixGoodsEvent  $event
     * @return void
     */
    public function handle(FixCPrefixGoodsEvent $event)
    {
        $map = with(new DataHelper)->convertToCGoods($event->getTargetCodes(), $event->getBeforeDays());

        return $this->genReturnMsg($map);
    }

    protected function genReturnMsg(array $map)
    {
        return str_replace(['=', '&'], ['->', ', '], http_build_query($map));
    }
}
