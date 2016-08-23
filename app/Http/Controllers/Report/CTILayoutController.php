<?php

namespace App\Http\Controllers\Report;

use App\Export\CTILayout\CtiExport;
use App\Export\CTILayout\FlapExport;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\CTI\Campaign;
use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use Artisan;
use Input;

class CTILayoutController extends Controller
{
	public function index()
    {
        $corpCodes = explode(',', env('CORPS'));

        $emps = HRS_Employee::findByCorps($corpCodes);

        $corps = Processor::getArrayResult(Processor::table('FAS_Corp')->select('Name, Code')->whereIn('Code', $corpCodes));

        $campaigns = Campaign::findValid();

        return view('report.ctilayout.index', [
            'corps' => $corps,
            'empCorpGroups' => array_group($emps, 'FName'),
            'campaigns' => $campaigns
        ]);
    }

    public function flap(FlapExport $export)
    {
        return $this->download($export);
    }

    public function cti(CtiExport $export)
    {
        return $this->download($export);
    }

    protected function download($export)
    {
        set_time_limit(0);
        
        $file = $export->handleExport()->getFile();

        if (!file_exists($file)) {
            throw new \Exception('Export CSV File dismiss!');
        }        

        return response()->download($file);
    }

    public function campaign()
    {
        Artisan::call('fv:importcampaign');
        
        $result = Artisan::output();

        $ifr = '##########!!!!!!!!!!';
        $start = strpos($result, $ifr) + strlen($ifr);
        $end = strpos($result, '!!!!!!!!!!##########');
        $length = $end - $start;

        $file = substr($result, $start, $length);

        return response()->download($file);
    }
}