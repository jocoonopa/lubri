<?php

namespace App\Console;

use Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        \App\Console\Commands\FV\Import\Campaign::class,
        \App\Console\Commands\FV\Import\Order::class,
        \App\Console\Commands\FV\Import\Product::class,
        \App\Console\Commands\FV\Import\Member::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Log::info('Command Test'. time());
        })->everyMinute();
    }
}
