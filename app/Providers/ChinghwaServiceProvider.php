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
    public function boot()
    {
        $this->composeHeader();
    }

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

        App::when('App\Export\CTILayout\CtiExportFileWriter')
          ->needs('App\Export\Mould\FVMould')
          ->give('App\Export\Mould\FVListMould');
    }

    protected function composeHeader()
    {
        return view()->composer('common.header', 'App\Http\Composer\ViewChinghwaComposer');
    }
}
