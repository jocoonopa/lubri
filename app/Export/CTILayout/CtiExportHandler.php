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
            'campaignCD' => !empty(Input::get('campaign_cd')) ? explode(',', trim(Input::get('campaign_cd'))) : [],
            'assignDate' => trim(Input::get('assign_date'))
        ]);

        $sheetName = 'export';

        $export->sheet($sheetName, $this->getSheetCallback($callLists));           

        return $export;
    }

    protected function getSheetCallback($callLists)
    {
        return function ($sheet) use ($callLists) {
            foreach ($callLists as $calllist) {                   
                $sheet->appendRow($calllist);
            }     
        };
    }
}