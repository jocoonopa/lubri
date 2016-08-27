<?php

namespace App\Http\Controllers\Report;

use App\Export\CTILayout\CtiExport;
use App\Export\CTILayout\FlapExport;
use App\Export\FV\Sync\Helper\Composer\ConditionComposer;
use App\Export\FV\Sync\Helper\ExecuteAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\FV\FVSyncListRequest;
use App\Http\Requests\FV\FVSyncMemberRequest;
use App\Model\Log\FVSyncQue;
use App\Model\Log\FVSyncType;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\CTI\Campaign;
use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use Artisan;
use Auth;
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
    
    public function flap(FlapExport $export)
    {
        return $this->download($export);
    }

    public function cti(CtiExport $export)
    {
        return $this->download($export);
    }

    public function syncMember(FVSyncMemberRequest $request)
    {
        return $this->createDelayQue(FVSyncType::ID_MEMBER, ConditionComposer::composeMixedConditions());
    }

    public function syncList(FVSyncListRequest $request)
    {
        return $this->createDelayQue(FVSyncType::ID_LIST, ConditionComposer::composeEngConditions());
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

    protected function createDelayQue($typeId, $conditions)
    {
        $que = with(new FVSyncQue)->sculpDelay($typeId, $conditions, Auth::user()->id);
        $que->save();

        Session::flash('success', "延時同步排程建立完成:{$que->id}");

        return redirect()->action('Report\CTILayoutController@index');
    }
}