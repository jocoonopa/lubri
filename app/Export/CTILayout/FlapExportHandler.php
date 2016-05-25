<?php

namespace App\Export\CTILayout;

use App\Model\State;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use App\Utility\Chinghwa\ORM\CTI\Campaign;
use App\Utility\Chinghwa\ORM\CTI\CampaignCallList;
use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use App\Export\Mould\FVMemberMould;
use Input;

class FlapExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    protected $mould;

    /**
     * ExcelHelper::rmi('Z') === 25
     * 
     * @param  App\Export\CTILayout\Export $export
     * @return App\Export\CTILayout\Export $export
     */
    public function handle($export)
    {
        $this->setMould(new FVMemberMould);

        $callLists = CampaignCallList::fetchCtiRes([
            'agentCD'    => !empty(Input::get('code')) ? explode(',', trim(Input::get('code'))) : [], 
            'sourceCD'   => !empty(Input::get('source_cd')) ? explode(',', trim(Input::get('source_cd'))) : [], 
            'campaignCD' => !empty(Input::get('campaign_cd')) ? explode(',', trim(Input::get('campaign_cd'))) : [],
            'assignDate' => trim(Input::get('assign_date'))
        ]);

        return $export->setFile($this->appendToFile( $this->getMembers($callLists, ['sourceCD' => Input::get('source_cd')])));
    }

    protected function appendToFile(array $members)
    {
        if (!file_exists(storage_path('excel/exports/ctilayout/'))) {
            mkdir(storage_path('excel/exports/ctilayout/'), 0777);
        }

        $fname = storage_path('excel/exports/ctilayout/') . str_replace(',', '-', Input::get('campaign_cd')) . '_' . time() . '.csv';

        $file = fopen($fname, 'w');

        foreach ($members as $member) {
            $appendStr = implode(',', $this->getMould()->getRow($member));
            $appendStr = cb5($appendStr);

            fwrite($file, $appendStr . "\r\n");
        }   

        fclose($file);

        return $fname;
    }

    protected function getMembers(array $callLists, array $options)
    {
        return empty($callLists) ? $this->getMemberDependOnFlap($options) : $this->getMemberDependBothFlapAndCTI($callLists, $options);   
    }

    protected function filterMember($member)
    {
        return (NULL !== $member && $this->inCorps($member));
    }

    protected function getMemberDependOnFlap(array $options)
    {  
        return array_filter($this->getCTILayoutData(array_get($options, 'sourceCD')), [$this, 'filterMember']);
    }

    protected function getMemberDependBothFlapAndCTI($callLists, $options)
    {
        $members = [];

        foreach ($callLists as $calllist) {           
            $member              = array_get($this->getCTILayoutData(array_get($calllist, 'SourceCD')), 0);    
            $member['AgentCD']   = array_get($calllist, 'AgentCD');
            $member['AgentName'] = array_get($calllist, 'AgentName');

            $members[] = $member;
        }  

        return array_filter($members, [$this, 'filterMember']);
    }

    public function getCTILayoutData($memberCode)
    {
        $codes = explode(',', trim($memberCode));

        $sql = str_replace('$memberCode', sqlInWrap($codes), Processor::getStorageSql('CTILayout.sql'));
        
        return Processor::getArrayResult($sql);        
    }

    public function getCampaigns()
    {
        return Campaign::findValid();
    }

    protected function inCorps(array $member)
    {
        $corpStr = trim(Input::get('corps'));
        $corps = !empty($corpStr) ? explode(',', $corpStr) : [];

        if (empty($corps)) {
            return true;
        }

        return in_array(array_get($member, '部門'), $corps);
    }

    /**
     * Gets the value of mould.
     *
     * @return mixed
     */
    public function getMould()
    {
        return $this->mould;
    }

    /**
     * Sets the value of mould.
     *
     * @param mixed $mould the mould
     *
     * @return self
     */
    protected function setMould($mould)
    {
        $this->mould = $mould;

        return $this;
    }
}