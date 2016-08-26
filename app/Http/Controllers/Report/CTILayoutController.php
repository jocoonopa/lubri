<?php

namespace App\Http\Controllers\Report;

use App\Export\CTILayout\CtiExport;
use App\Export\CTILayout\FlapExport;
use App\Export\FV\Sync\Helper\ExecuteAgent;
use App\Http\Controllers\Controller;
use App\Model\Log\FVSyncType;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\CTI\Campaign;
use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use Artisan;
use Session;

class CTILayoutController extends Controller
{
	public function index()
    {
        $corpCodes = explode(',', env('CORPS'));
        $emps      = HRS_Employee::findByCorps($corpCodes);
        $corps     = Processor::getArrayResult(Processor::table('FAS_Corp')->select('Name, Code')->whereIn('Code', $corpCodes));
        $campaigns = Campaign::findValid();

        return view('report.ctilayout.index', [
            'corps'         => $corps,
            'empCorpGroups' => array_group($emps, 'FName'),
            'campaigns'     => $campaigns
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

    public function syncMember(FlapExport $export)
    {
        return $this->command(FVSyncType::VIGATYPE_MEMBER, 'FVSYNC_MEMBER_STORAGE_PATH', $export);
    }

    public function syncList(CtiExport $export)
    {
        return $this->command(FVSyncType::VIGATYPE_LIST, 'FVSYNC_CALLLIST_STORAGE_PATH', $export);
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

    protected function command($vigaCmdType, $enVar, $export)
    {
        set_time_limit(0);

        $fname    = $export->handleExport()->getFile();
        $destName = join(DIRECTORY_SEPARATOR, [env($enVar), basename($fname)]);

        rename($fname, $destName);

        pclose(popen('start ' . ExecuteAgent::genCmd($vigaCmdType, $destName), 'r'));

        Session::flash('success', "同步任務執行完畢[{$destName}]");

        return redirect()->action('Report\CTILayoutController@index');
    }

    public function campaign()
    {
        Artisan::call('fv:importcampaign');
        
        $result = Artisan::output();
        $ifr    = '##########!!!!!!!!!!';
        $start  = strpos($result, $ifr) + strlen($ifr);
        $end    = strpos($result, '!!!!!!!!!!##########');
        $length = $end - $start;
        $file   = substr($result, $start, $length);

        return response()->download($file);
    }
}