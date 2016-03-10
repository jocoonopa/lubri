<?php

namespace App\Http\Controllers\Flap\CCS_OrderIndex;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Export\PromoteShipmentExport;

class PromoteShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('flap.ccsorderindex.promoteshipment.index');
    }

    public function export(PromoteShipmentExport $export)
    {
        return $export->handleExport()->export();
    }
}
