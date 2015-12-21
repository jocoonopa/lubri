<?php

namespace App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Copy;

use App\Events\Flap\PIS_Goods\CopyToCometrust\CopyEvent;
use App\Utility\Chinghwa\Helper\PISGoodsImportQueryHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CopyFromErpEvent
{
    protected $qh;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PISGoodsImportQueryHelper $qh)
    {
        $this->qh = $qh;
    }

    /**
     * Handle the event.
     *
     * @param  CopyEvent  $event
     * @return void
     */
    public function handle(CopyEvent $event)
    {
        $goodses = $event->getGoodses();
        
        array_walk($goodses, function (&$goods, $key) {
            if (false !== $goods['ctCode']) {
                unset($goodses[$key]);
            }
        });

        $this->qh->copy(array_pluck($goodses, 'Code'));

        return $this->genSuccessMsg($goodses);
    }

    protected function genSuccessMsg(array $goodses)
    {
        $msgs = [];

        foreach ($goodses as $goods) {
            $msgs[] = "{$goods['Code']}->CT{$goods['Code']}";
        }

        return implode(',', $msgs);
    }
}
