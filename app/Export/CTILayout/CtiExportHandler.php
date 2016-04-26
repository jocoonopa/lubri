<?php

namespace App\Export\CTILayout;

use App\Utility\Chinghwa\ORM\CTI\CampaignCallList;
use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use Input;

class CtiExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    /**
     * ExcelHelper::rmi('Z') === 25
     * 
     * @param  App\Export\CTILayout\Export $export
     * @return App\Export\CTILayout\Export $export
     */
    public function handle($export)
    {
        return $this->single($export)->export();
    }

    protected function single($export)
    {
        $callLists = CampaignCallList::fetchCtiRes([
            'agentCD'    => !empty(Input::get('code')) ? explode(',', trim(Input::get('code'))) : [], 
            'sourceCD'   => !empty(Input::get('source_cd')) ? explode(',', trim(Input::get('source_cd'))) : [], 
            'campaignCD' => !empty(Input::get('campaign_cd')) ? explode(',', trim(Input::get('campaign_cd'))) : [],
            'assignDate' => trim(Input::get('assign_date'))
        ]);

        $sheetName = 'export';

        $export->sheet($sheetName, $this->getSheetCallback($callLists));           

        return $export;
    }

    protected function inCorps(array $calllist)
    {        
        $corps = Input::get('corps');

        if (empty($corps)) {
            return true;
        }

        $member = array_get($this->getCTILayoutData(array_get($calllist, 'SourceCD')), 0);

        return in_array(array_get($member, '部門'), $corps);
    }

    public function getCTILayoutData($memberCode)
    {
        return Processor::getArrayResult(str_replace('$memberCode', $memberCode, Processor::getStorageSql('CTILayout.sql')));
    }

    protected function getSheetCallback($callLists)
    {
        return function ($sheet) use ($callLists) {
            $sheet->appendRow($this->getTitle());

            foreach ($callLists as $calllist) {  
                if (!$this->inCorps($calllist)) {
                    continue;
                }   

                $sheet->appendRow($calllist);
            }     
        };
    }

    protected function getTitle() 
    {
        return ['PLACEHOLDER'];
    }
}