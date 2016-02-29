<?php

namespace App\Http\Controllers\Report;

use App\Events\Report\RetailSalePersonFormula\ReportEvent;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Export\RetailSalePersonExport;
use Event;
/**
 * 1. Move to Export and Handler
 * 2. Remove origin RetailSalePersonTypeController and Relate class
 * 3. Refact, minify the complexity
 * 4. Add border and color
 */
class RetailSalePersonFormulaController extends Controller
{
    public function process(RetailSalePersonExport $export)
    {
        return Event::fire(new ReportEvent($export->handleExport()));
    }
}