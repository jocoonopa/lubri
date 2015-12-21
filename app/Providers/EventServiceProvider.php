<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Handlers\Events\EmailSomeEvent',
            'App\Handlers\Events\LogSomeEvent',
        ],

        'App\Events\Flap\CCS_OrderIndex\PrefixEvent' => [
            'App\Handlers\Events\Flap\CCS_OrderIndex\PrefixEvent\ModifyOrderEvent',
            'App\Handlers\Events\Flap\CCS_OrderIndex\PrefixEvent\MailNotifyEvent'
        ],

        'App\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent' => [
            'App\Handlers\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent\CheckMassAssignEvent',
            'App\Handlers\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent\ModifyGoodsEvent',
            'App\Handlers\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent\MailNotifyEvent'
        ],

        'App\Events\Flap\PIS_Goods\CopyToCometrust\FindEvent' => [
            'App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Find\FindFromErpEvent',
            'App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Find\ResultAssembleEvent'
        ],

        'App\Events\Flap\PIS_Goods\CopyToCometrust\CopyEvent' => [
            'App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Find\FindFromErpEvent',
            'App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Find\ResultAssembleEvent',
            'App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Copy\CopyFromErpEvent',
            'App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Copy\ResultAssembleEvent',
            'App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Copy\MailNotifyEvent'
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);
    }
}
