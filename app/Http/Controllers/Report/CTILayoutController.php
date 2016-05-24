<?php

namespace App\Http\Controllers\Report;

use App\Export\CTILayout\FlapExport;
use App\Export\CTILayout\CtiExport;
use App\Http\Controllers\Controller;
use Input;

class CTILayoutController extends Controller
{
	public function index()
    {
        $code       = Input::get('code', '20160202');
        $assignDate = Input::get('assign_date', '20160415');
        $campaignCD = Input::get('campaign_cd', 'OB_6713');
        
        $qStr       = "code={$code}&assign_date={$assignDate}&campaign_cd={$campaignCD}";
        $flapUrl    = "/report/ctilayout/flap?{$qStr}";
        $ctiUrl     = "/report/ctilayout/cti?{$qStr}";

        return view('report.ctilayout.index', [
            'flapUrl'    => $flapUrl, 
            'ctiUrl'     => $ctiUrl,
            'code'       => $code,
            'assignDate' => $assignDate,
            'campaignCD' => $campaignCD
        ]);
    }

    public function flap(FlapExport $export)
    {
        set_time_limit(0);
        
        $export->handleExport();
    }

    public function cti(CtiExport $export)
    {
        set_time_limit(0);
        
        $export->handleExport();
    }
}