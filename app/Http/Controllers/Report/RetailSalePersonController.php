<?php

namespace App\Http\Controllers\Report;

use App\Events\Report\RetailSalePerson\ReportEvent;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Export\RetailSalePersonExport;
use Event;

class RetailSalePersonController extends Controller
{
    public function process(RetailSalePersonExport $export)
    {
        return Event::fire(new ReportEvent($export->handleExport()));
    }
}