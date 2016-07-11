<?php

namespace App\Http\Controllers\Report;

use App\Events\Report\RetailSales\ReportEvent;
use App\Export\RetailSales\Export;
use App\Http\Controllers\Controller;
use Event;

class RetailSalesController extends Controller
{
    public function index()
    {
        return redirect('/pos/store_goal');
    }

    public function process(Export $export)
    {       
        set_time_limit(0);

        return Event::fire(new ReportEvent($export->handleExport()));
    }

    public function download(Export $export)
    {
        set_time_limit(0);
        
        return $export->handleExport()->export();
    }
}