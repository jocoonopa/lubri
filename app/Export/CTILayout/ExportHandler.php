<?php

namespace App\Export\CTILayout;

use App\Model\State;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use App\Utility\Chinghwa\ORM\CTI\Campaign;
use App\Utility\Chinghwa\ORM\CTI\CampaignCallList;
use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use Input;

class ExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    /**
     * ExcelHelper::rmi('Z') === 25
     * 
     * @param  App\Export\CTILayout\Export $export
     * @return App\Export\CTILayout\Export $export
     */
    public function handle($export)
    {
        $emp = HRS_Employee::first(['code' => Input::get('code', '20160203')]);
        
        $campaigns = $this->getCampaigns();

        foreach ($campaigns as $campaign) {
            $callLists = $this->getCampaignCallList(array_get($emp, 'Code'), trim(array_get($campaign, 'CampaignCD')));

            $sheetName = trim(array_get($campaign, 'DefSchemaCD')) . '##' . trim(array_get($campaign, 'CampaignCD'));

            if (!$callLists) {
                continue;
            }

            $export->sheet($sheetName, $this->getSheetCallback($callLists));           
        }

        $export->export();
    }

    protected function getSheetCallback($callLists)
    {
        return function ($sheet) use ($callLists) {
            $sheet->appendRow($this->getExportHead());

            foreach ($callLists as $calllist) {                   
                $member = array_get($this->getCTILayoutData(array_get($calllist, 'SourceCD')), 0);

                $hd = $this->getHospitalAndPeriod([array_get($member, '備註'), array_get($member, '備註1'), array_get($member, '備註2')]);

                $sheet->appendRow($this->getFilterMember($member, $hd));
            }     
        };
    }

    protected function getFilterMember($member, $hd)
    {
        $this->replaceWithNewCityState($member);

        return [
            array_get($member, '會員代號'),
            array_get($member, '會員姓名'),
            array_get($member, '性別'),
            array_get($member, '生日'),
            array_get($member, '身份證號'), 
            array_get($member, '連絡電話'), 
            array_get($member, '公司電話'), 
            array_get($member, '手機號碼'),
            array_get($member, '縣市'), 
            array_get($member, '區'),
            array_get($member, '郵遞區號'),
            array_get($member, '地址'),
            array_get($member, 'e-mail'), 
            array_get($member, '開發人代號'), 
            array_get($member, '開發人姓名'), 
            array_get($member, '會員類別代號'), 
            array_get($member, '會員類別名稱'), 
            array_get($member, '區別代號'),
            array_get($member, '區別名稱'), 
            array_get($member, '首次購物金額'),
            array_get($member, '首次購物日'), 
            array_get($member, '最後購物金額'),
            array_get($member, '最後購物日'), 
            array_get($member, '累積購物金額'),
            array_get($member, '累積紅利點數'), 
            array_get($member, '輔翼會員參數'),
            array_get($hd, 'period'),
            array_get($hd, 'hospital')
        ];
    }

    public function getCampaigns()
    {
        return Campaign::findValid();
    }

    protected function replaceWithNewCityState(&$member) 
    {
        $state = State::findByZipcode(array_get($member, '郵遞區號'))->first();

        if ($state) {
            $member['縣市'] = $state->city()->first()->name;
            $member['區'] = $state->name;
        }
    }

    public function getCampaignCallList($agentCD, $campaignCD)
    {
        return CampaignCallList::find(['agentCD' => $agentCD, 'campaignCD' => $campaignCD]);
    }

    public function getCTILayoutData($memberCode)
    {
        return Processor::getArrayResult(str_replace('$memberCode', $memberCode, Processor::getStorageSql('CTILayout.sql')));
    }

    protected function getExportHead()
    {
        return [
            '會員代號', 
            '會員姓名', 
            '性別', 
            '生日', 
            '身份證號', 
            '連絡電話', 
            '公司電話', 
            '手機號碼', 
            '縣市', 
            '區', 
            '郵遞區號', 
            '地址', 
            'e-mail', 
            '開發人代號', 
            '開發人姓名', 
            '會員類別代號', 
            '會員類別名稱', 
            '區別代號', 
            '區別名稱', 
            '首次購物金額', 
            '首次購物日', 
            '最後購物金額', 
            '最後購物日', 
            '累積購物金額', 
            '累積紅利點數', 
            '輔翼會員參數', 
            '預產期', 
            '醫院'
        ];
    }

    protected function getHospitalAndPeriod($memos)
    {
        $res = ['hospital' => '', 'period' => ''];
        $arr = [];

        foreach ($memos as $memo) {
            $arr = explode(';', $memo);

            if (3 >= count($arr)) {
                continue;
            }

            break;
        }

        if (4 > count($arr)) {            
            return $res;
        }

        foreach ($arr as $val) {
            if (false !== strpos($val, '生產醫院')) {
                $res['hospital'] = trim(str_replace('生產醫院:', '', $val));
            }

            if (false !== strpos($val, '預產期')) {
                $res['period'] = preg_replace('/[^0-9]/', '', $val);
            }
        }

        return $res;
    }
}