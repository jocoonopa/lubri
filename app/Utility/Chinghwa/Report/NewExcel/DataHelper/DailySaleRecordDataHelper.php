<?php

namespace App\Utility\Chinghwa\Report\NewExcel\DataHelper;

use App\Utility\Chinghwa\Report\DailySaleRecord;
use App\Utility\Chinghwa\Helper\Report\DailySaleRecordHelper;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class DailySaleRecordDataHelper
{
	public $erpGroups = [];
    public $posGroups = [];
    public $erpStatistics = [];
    public $posStatistics = [];
    public $totalPospGroup = [];
    public $ctiCallLog = [];

    protected $date;

    public function __construct(\DateTime $date)
    {
        $this->date = $date;
        $this->fetchErpGroups()->fetchCtiCallLog()->fetchPosGroups();
    }

    public function fetchPosGroups()
    {
        $this->posGroups = $this->splitPosDataByGroup(Processor::getArrayResult($this->getPOSQuery(), 'Pos'));

        return $this;
    }

    public function fetchCtiCallLog()
    {
        $this->ctiCallLog = Processor::getArrayResult($this->getCtiQuery(), 'Cti');

        return $this;
    }

    public function fetchErpGroups()
    {
        $this->erpGroups = $this->splitErpDataByGroup(Processor::getArrayResult($this->getErpQuery(), 'Erp'));

        return $this;
    }

    public function getERPQuery()
    {
        return str_replace(
            ['$startDate', '$endDate'],
            [$this->date->modify('first day of this month')->format('Ymd'), $this->date->modify('last day of this month')->format('Ymd')],
            file_get_contents(__DIR__ . '/../../../../../../storage/sql/DailySaleRecord/ERP.sql')
        );
    }

    public function getPOSQuery()
    {
        return str_replace(
            ['$startDate', '$endDate'],
            [$this->date->modify('first day of this month')->format('Ymd'), $this->date->modify('last day of this month')->format('Ymd')],
            file_get_contents(__DIR__ . '/../../../../../../storage/sql/DailySaleRecord/POS.sql')
        );
    }

    public function getCTIQuery()
    {
        return str_replace(
            ['$startDate', '$endDate'],
            [$this->date->modify('first day of this month')->format('Ymd'), $this->date->modify('last day of this month')->format('Ymd')],
            file_get_contents(__DIR__ . '/../../../../../../storage/sql/DailySaleRecord/CTI.sql')
        );
    }

    /**
     * 將 ERP DB 撈取到的資料分類，非直效的部分算在外部通路
     *
     * 將分類好的資料傳回
     * 
     * @param  array  $erpData
     * @return array  $groupIndexData       
     */
    public function splitErpDataByGroup(array $erpData)
    {
        $groupIndexData = [];

        $list = DailySaleRecordHelper::getErpGroupList();

        foreach ($erpData as $erp) {
            $groupName = in_array($erp[DailySaleRecord::ERP_CORPCODE_COLUMN], $list) ? $erp[DailySaleRecord::ERP_CORPCODE_COLUMN] : DailySaleRecord::ERP_OUTTUNNEL;
            
            $groupIndexData[$groupName][] = $erp;
        }

        return $groupIndexData;
    }    

    public function splitPosDataByGroup(array $posData)
    {
        $groupIndexData = [];

        $list = DailySaleRecordHelper::getPosGroupList();

        foreach ($posData as $pos) {
            $groupName = (in_array($pos[DailySaleRecord::POS_CORPCODE_COLUMN], $list)) 
                ? $pos[DailySaleRecord::POS_CORPCODE_COLUMN] : DailySaleRecord::POS_NONEXIST_GROUP;

            $groupIndexData[$groupName][] = $pos;
        }

        return $groupIndexData;
    }

    public function getBundleTotal(array $totalGroup, $bundleName)
    {
        $allGroup = [];
        $allGroup['部門'] = $bundleName;
        $allGroup['人員代碼'] = '';
        $allGroup['姓名'] = '';
        $allGroup['會員數'] = 0; 
        $allGroup['訂單數'] = 0; 
        $allGroup['淨額'] = 0; 
        $allGroup['會員均單'] = 0;
        $allGroup['訂單均價'] = 0;
        $allGroup['撥打會員數'] = 0;
        $allGroup['撥打通數'] = 0;
        $allGroup['撥打秒數'] = 0;
        $allGroup['工作日'] = 0; 

        foreach ($totalGroup as $group) {
            $allGroup['會員數'] += (int) getArrayVal($group, '會員數', 0);
            $allGroup['訂單數'] += (int) getArrayVal($group, '訂單數', 0);
            $allGroup['淨額'] += (int) getArrayVal($group, '淨額', 0);
            $allGroup['撥打會員數'] += (int) getArrayVal($group, '撥打會員數', 0);
            $allGroup['撥打通數'] += (int) getArrayVal($group, '撥打通數', 0);
            $allGroup['撥打秒數'] += (int) getArrayVal($group, '撥打秒數', 0);
            $allGroup['工作日'] += (int) getArrayVal($group, '工作日', 0);
        }

        $allGroup['會員均單'] = (0 == $allGroup['會員數']) ? 0 : (int) @($allGroup['淨額']/$allGroup['會員數']);
        $allGroup['訂單均價'] = (0 == $allGroup['訂單數']) ? 0 : (int) @($allGroup['淨額']/$allGroup['訂單數']);

        return $allGroup;
    }

    public function updateErpGroupTotal(array $agentRow, $groupCode)
    {
        if (!array_key_exists($groupCode, $this->erpStatistics)) {
            $this->erpStatistics[$groupCode]['部門'] = ((DailySaleRecord::ERP_OUTTUNNEL === $groupCode) ? '外部通路' : $agentRow['部門']). '合計';
            $this->erpStatistics[$groupCode] = array_merge($this->erpStatistics[$groupCode], $this->initTotalGroup());
        }

        $this->erpStatistics[$groupCode]['會員數'] += (int) getArrayVal($agentRow, '會員數', 0);
        $this->erpStatistics[$groupCode]['訂單數'] += (int) getArrayVal($agentRow, '訂單數', 0);
        $this->erpStatistics[$groupCode]['淨額'] += (int) getArrayVal($agentRow, '淨額', 0);
        $this->erpStatistics[$groupCode]['會員均單'] = (int) $this->erpStatistics[$groupCode]['淨額']/$this->erpStatistics[$groupCode]['會員數'];
        $this->erpStatistics[$groupCode]['訂單均價'] = (int) $this->erpStatistics[$groupCode]['淨額']/$this->erpStatistics[$groupCode]['訂單數'];
        $this->erpStatistics[$groupCode]['撥打會員數'] += (int) getArrayVal($agentRow, '撥打會員數', 0);
        $this->erpStatistics[$groupCode]['撥打通數'] += (int) getArrayVal($agentRow, '撥打通數', 0);
        $this->erpStatistics[$groupCode]['撥打秒數'] += (int) getArrayVal($agentRow, '撥打秒數', 0);
        $this->erpStatistics[$groupCode]['工作日'] += (int) getArrayVal($agentRow, '工作日', 0);

        return $this;
    }

    public function updatePosGroupTotal(array $display, $groupCode)
    {
        if (!array_key_exists($groupCode, $this->posStatistics)) {
            $this->posStatistics[$groupCode]['部門'] = $display['部門'];
            $this->posStatistics[$groupCode] = array_merge($this->posStatistics[$groupCode], $this->initTotalGroup());
        }

        $this->posStatistics[$groupCode]['會員數'] += (int) getArrayVal($display, '會員數', 0);
        $this->posStatistics[$groupCode]['訂單數'] += (int) getArrayVal($display, '訂單數', 0);
        $this->posStatistics[$groupCode]['淨額'] += (int) getArrayVal($display, '淨額', 0);
        $this->posStatistics[$groupCode]['會員均單'] = (int) $this->posStatistics[$groupCode]['淨額']/$this->posStatistics[$groupCode]['會員數'];
        $this->posStatistics[$groupCode]['訂單均價'] = (int) $this->posStatistics[$groupCode]['淨額']/$this->posStatistics[$groupCode]['訂單數'];
        $this->posStatistics[$groupCode]['撥打會員數'] += (int) getArrayVal($display, '撥打會員數', 0);
        $this->posStatistics[$groupCode]['撥打通數'] += (int) getArrayVal($display, '撥打通數', 0);
        $this->posStatistics[$groupCode]['撥打秒數'] += (int) getArrayVal($display, '撥打秒數', 0);
        $this->posStatistics[$groupCode]['工作日'] += (int) getArrayVal($display, '工作日', 0);

        return $this;
    }

    public function initTotalGroup()
    {
        $prototypeGroup = [];

        $prototypeGroup['人員代碼'] = '';
        $prototypeGroup['姓名'] = '';
        $prototypeGroup['會員數'] = 0;
        $prototypeGroup['訂單數'] = 0;
        $prototypeGroup['淨額'] = 0;
        $prototypeGroup['會員均單'] = 0;
        $prototypeGroup['訂單均價'] = 0;
        $prototypeGroup['撥打會員數'] = 0;
        $prototypeGroup['撥打通數'] = 0;
        $prototypeGroup['撥打秒數'] = 0;
        $prototypeGroup['工作日'] = 0; 

        return $prototypeGroup;
    }

    public function genAgentRow(array $agent)
    {
        $cti = $this->getCtiFromErp($agent[DailySaleRecord::CTI_JOIN_COLUMN]);

        return [
            '部門' => $agent['部門'],
            '人員代碼' => $agent[DailySaleRecord::CTI_JOIN_COLUMN],
            '姓名' => $agent['姓名'],      
            '會員數' => $agent['會員數'],     
            '訂單數' => $agent['訂單數'],     
            '淨額' => $agent['淨額'],      
            '會員均單' => (int) $agent['淨額']/$agent['會員數'],    
            '訂單均價' => (int) $agent['淨額']/$agent['訂單數'],  
            '撥打會員數' => $cti['撥打會員數'],   
            '撥打通數' => $cti['撥打通數'], 
            '撥打秒數' => $cti['撥打秒數'], 
            '工作日' => $cti['工作日']
        ];
    }

    public function genPosDisplay(array $agent)
    {
        return [
            '部門' => $agent['門市名稱'],
            '人員代碼' => $agent['營業員代號'],
            '姓名' => $agent['營業員名稱'],      
            '會員數' => $agent['會員數'],     
            '訂單數' => $agent['訂單數'],     
            '淨額' => $agent['金額'],      
            '會員均單' => (int) $agent['金額']/$agent['會員數'],    
            '訂單均價' => (int) $agent['金額']/$agent['訂單數'],  
            '撥打會員數' => '',   
            '撥打通數' => '', 
            '撥打秒數' => '', 
            '工作日' => ''
        ];
    }

    public function getCtiFromErp($code)
    {       
        $key = array_search(trim($code), array_column($this->ctiCallLog, DailySaleRecord::CTI_JOIN_COLUMN));

        return (false !== $key) ? $this->ctiCallLog[$key] : null;
    }
}