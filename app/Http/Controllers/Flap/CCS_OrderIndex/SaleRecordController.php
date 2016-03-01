<?php

namespace App\Http\Controllers\Flap\CCS_OrderIndex;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Export\SaleRecordExport;

class SaleRecordController extends Controller
{
    public function process(SaleRecordExport $export)
    {
        return $export->handleExport()->export();   
    }
}
