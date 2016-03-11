<?php

namespace App\Http\Controllers\Report;

use App\Events\Report\DailySaleRecord\ReportEvent;
use App\Http\Controllers\Controller;
use App\Export\DailySaleRecord\Export;
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
}