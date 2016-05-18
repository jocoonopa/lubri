<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use Queue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::getIron()->ssl_verifypeer = false;
        Validator::extend('cellphone', 'App\Http\Validator\ImportContentValidator@cellphone');
        Validator::extend('tel', 'App\Http\Validator\ImportContentValidator@tel');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
