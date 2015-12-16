<?php 

namespace App\Providers;

use App;
use App\Utility\Chinghwa;
use Illuminate\Support\ServiceProvider;

class ChinghwaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(){}

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('chinghwa', function() {
            return new Chinghwa;
        });
    }
}
