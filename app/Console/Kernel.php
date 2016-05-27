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
        \App\Console\Commands\FVSync\Campaign::class,
        \App\Console\Commands\FVSync\Order::class,
        \App\Console\Commands\FVSync\Product::class,
        \App\Console\Commands\FVSync\Member::class,
        \App\Console\Commands\FVImport\Campaign::class,
        \App\Console\Commands\FVImport\Order::class,
        \App\Console\Commands\FVImport\Product::class,
        \App\Console\Commands\FVImport\Member::class
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
