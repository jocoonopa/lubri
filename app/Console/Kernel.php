<?php

namespace App\Console;

use Artisan;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\FV\Sync\Campaign::class,
        \App\Console\Commands\FV\Sync\Order::class,
        \App\Console\Commands\FV\Sync\Product::class,
        \App\Console\Commands\FV\Sync\Member::class,
        \App\Console\Commands\FV\Sync\CallList::class,
        \App\Console\Commands\FV\Sync\Calllog::class,
        \App\Console\Commands\FV\Sync\ListRepair::class,
        \App\Console\Commands\FV\Sync\DoDelay::class,
        \App\Console\Commands\FV\Import\Campaign::class,
        \App\Console\Commands\FV\Import\Order::class,
        \App\Console\Commands\FV\Import\Product::class,
        \App\Console\Commands\FV\Import\Member::class,
        \App\Console\Commands\FV\Import\CallList::class,
        \App\Console\Commands\FV\Import\Calllog::class,
        \App\Console\Commands\FV\Import\OrderPipe::class,
        \App\Console\Commands\FV\Notify\ErrorInspecter::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (true === env('SCHEDULE_TEST')) {
            $schedule->call(function () {
                Log::info('Schedule Test: '. time());
            })->everyMinute();
        }

        if (true === env('SCHEDULE_FV')) {
            $schedule->command('fv:syncmember')
                ->everyTenMinutes()
                ->when(function () {
                    $dt = Carbon::now();

                    return (8 <= $dt->hour && 19 >= $dt->hour);
                });

            $schedule->command('fv:synproduct')
                ->twiceDaily(0, 12)
                ->before(function () use ($schedule) {
                    Artisan::call('fv:syncmember');
                })
                ->after(function () use ($schedule) {
                    Artisan::call('fv:syncorder');
                });

            $schedule->call(function () {
                Artisan::call('fv:synccampaign');
                Artisan::call('fv:synclist');
                Artisan::call('fv:synccalllog');
            })
            ->daily();
        }
    }
}
