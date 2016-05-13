<?php

namespace App\Http\Controllers\Report;

use App\Events\Report\DailySaleRecord\ReportEvent;
use App\Events\Report\DailySaleRecord\OnlyEcReportEvent;
use App\Events\Report\DailySaleRecord\OnlyCtiReportEvent;

use App\Export\DailySaleRecord\Export;
use App\Export\DailySaleRecord\OnlyEcExport;
use App\Export\DailySaleRecord\OnlyCtiExport; 

use App\Http\Controllers\Controller;
use Event;

class DailySaleRecordController extends Controller
{
    public function index()
    {   
        return view('basic.simple', ['title' => Export::REPORT_NAME, 'des' => NULL,'res' => NULL]);
    }

    public function process(Export $export)
    {
        return Event::fire(new ReportEvent($export->handleExport()));
    }

    public function onlyEc(OnlyEcExport $export)
    {
        return Event::fire(new ReportEvent($export->handleExport()));
    }

    public function onlyCti(OnlyCtiExport $export)
    {
        return Event::fire(new ReportEvent($export->handleExport()));
    }
}