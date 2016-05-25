<?php

namespace App\Http\Controllers\Report;

use App\Export\CTILayout\CtiExport;
use App\Export\CTILayout\FlapExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Input;
use Log;

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

        $file      = $export->handleExport()->getFile();
        $shellFile = storage_path('ps1/copy.ps1');
        $username  = env('AD_USERNAME');
        $password  = env('AD_PASSWORD');
        $dest      = ' ' . env('AD_DEST');

        if (!file_exists($shellFile)) {
            throw new \Exception('power shell dismiss : ' . $shellFile);
        }       

        Log::info(shell_exec("powershell -ExecutionPolicy Bypass -File {$shellFile} {$file} {$dest} {$username} {$password}"));

        if (!file_exists($file)) {
            throw new \Exception('Export CSV File dismiss!');
        }        

        return $this->response($file);
    }

    public function response($file)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'. basename($file) .'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        
        return readfile($file);
    }

    public function cti(CtiExport $export)
    {
        set_time_limit(0);
        
        $export->handleExport();
    }
}