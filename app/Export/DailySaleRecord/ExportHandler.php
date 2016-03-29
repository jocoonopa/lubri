<?php

namespace App\Export\DailySaleRecord;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;

class ExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
	const BORDER_RIGHT_RANGE = 'L';

    protected $date;
	protected $rowIndex = 1;
    protected $targetSheet;
    protected $dataHelper;

    public function handle($export)
    {
        $this->date = $export->getDate();

        $export->sheet($this->getSheetToLastOfMonthText(), $this->getSheetToLastOfMonthFunc())
            ->sheet($this->getSheetTodayText(), $this->getSheetTodayFunc())
            ->sheet($this->getSheetTilTodayText(), $this->getSheetTilTodayFunc())
            ->store('xlsx', storage_path('excel/exports'))
        ;

        return $export;
    }

    protected function getSheetToLastOfMonthText()
    {
        return '本月目前業績' . with(new Carbon('first day of this month'))->format('Ymd') . '-' . with(new Carbon('last day of this month'))->format('Ymd');
    }

    protected function getSheetTodayText()
    {
        return '今日業績' . Carbon::now()->modify('-1 days')->format('Ymd');
    }

    protected function getSheetTilTodayText()
    {
        return '本月累計至今日業績' . with(new Carbon('first day of this month'))->format('Ymd') . '-' . Carbon::now()->modify('-1 days')->format('Ymd');
    }

    /**
     * Excel 內容編輯動作
     * 
     * 1. ERP 資料貼上
     *     -> 直效資料貼上
     *     -> 外部通路資料貼上
     *     
     * 2. 直營門市資料貼上
     * 3. 所有資料總和貼上
     *
     * 資料的取得應該是伴隨欄位得貼上執行，即掛勾在欄位貼上的動作內
     * 
     * @return 
     */
    protected function getSheetToLastOfMonthFunc()
    {
        return function ($sheet) {
            $startDate = with(new Carbon('first day of this month'))->format('Ymd');
            $endDate = with(new Carbon('last day of this month'))->format('Ymd');

            $this
                ->setTargetSheet($sheet)
                ->setSheetBasicStyle()
                ->appendHead()->next()
                ->initDataHelper($startDate, $endDate)
                ->appendByIterateErpTelGroups()->prev()        
                ->appendTotalErpTelGroup()->next()->next()
                ->appendErpOuttunnel()
                ->appendByIteratePosGroups()
                ->appendTotalPosGroup()->next()
                ->appendAllSrcTotal()
            ;   
       };
    }

    /**
     * Excel 內容編輯動作
     * 
     * 1. ERP 資料貼上
     *     -> 直效資料貼上
     *     -> 外部通路資料貼上
     *     
     * 2. 直營門市資料貼上
     * 3. 所有資料總和貼上
     *
     * 資料的取得應該是伴隨欄位得貼上執行，即掛勾在欄位貼上的動作內
     * 
     * @return 
     */
    protected function getSheetTodayFunc()
    {
        return function ($sheet) {
            $this->date = Carbon::now()->modify('-1 days');
            $this->rowIndex = 1;
            $startDate = Carbon::now()->modify('-1 days')->format('Ymd');
            $endDate = $startDate;

            $this
                ->setTargetSheet($sheet)
                ->setSheetBasicStyle()
                ->appendHead()->next()
                ->initDataHelper($startDate, $endDate)
                ->appendByIterateErpTelGroups()->prev()        
                ->appendTotalErpTelGroup()->next()->next()
                ->appendErpOuttunnel()
                ->appendByIteratePosGroups()
                ->appendTotalPosGroup()->next()
                ->appendAllSrcTotal()
            ;   
       };
    }

    protected function getSheetTilTodayFunc()
    {
        return function ($sheet) {
            $this->date = Carbon::now()->modify('-1 days');
            $this->rowIndex = 1;

            $startDate = with(new Carbon('first day of this month'))->format('Ymd');
            $endDate = Carbon::now()->modify('-1 days')->format('Ymd');

            $this
                ->setTargetSheet($sheet)
                ->setSheetBasicStyle()
                ->appendHead()->next()
                ->initDataHelper($startDate, $endDate)
                ->appendByIterateErpTelGroups()->prev()        
                ->appendTotalErpTelGroup()->next()->next()
                ->appendErpOuttunnel()
                ->appendByIteratePosGroups()
                ->appendTotalPosGroup()->next()
                ->appendAllSrcTotal()
            ;   
       };
    }

    protected function initDataHelper($startDate, $endDate)
    {
        $this->dataHelper = new ExportHelper($this->date, $startDate, $endDate);

        return $this;
    }

    protected function next()
    {
        $this->rowIndex ++;

        return $this;
    }

    protected function prev()
    {
        $this->rowIndex --;

        return $this;
    }

    protected function getIndex()
    {
        return $this->rowIndex;
    }

    protected function appendByIteratePosGroups()
    {
        foreach ($this->dataHelper->posGroups as $groupCode => $group) {
            foreach ($group as $agent) {                       
                $this->next()->appendEachPosAgent($agent, $groupCode);
            }

            $this->next()->appendEachPosStoreStatistics($groupCode)->next();
        }

        return $this;
    }

    protected function appendRow(array $data, array $css)
    {
        $this->targetSheet->row($this->getIndex(), $data);
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) use ($css) {
            $cells->setBackground(getArrayVal($css, 'backgrounColor'))->setFontColor(getArrayVal($css, 'fontColor'));
        });

        return $this;
    }

    protected function appendTotalPosGroup()
    {
        return $this->appendRow($this->dataHelper->getBundleTotal($this->dataHelper->posStatistics, '直營門市合計'), [
            'backgrounColor' => '#D48005',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function appendAllSrcTotal()
    {
        return $this->appendRow($this->dataHelper->getBundleTotal(array_merge($this->dataHelper->erpStatistics, $this->dataHelper->posStatistics), '公司合計'), [
            'backgrounColor' => '#088A1E',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function appendEachPosAgent(array $agent, $groupCode)
    {
        $agnetRow = $this->dataHelper->genPosDisplay($agent);
        $this->dataHelper->updatePosStatistics($agnetRow, $groupCode);

        return $this->appendRow($agnetRow, [
            'backgrounColor' => '#827F7F',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function appendEachPosStoreStatistics($groupCode)
    {
        return $this->appendRow($this->dataHelper->posStatistics[$groupCode], [
            'backgrounColor' => '#CC0606',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function appendErpOuttunnelStatistics()
    {
        return $this->appendRow($this->dataHelper->erpStatistics[Export::ERP_OUTTUNNEL], [
            'backgrounColor' => '#CC0606',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function appendEachTelGroup($groupCode)
    {
        return $this->appendRow($this->dataHelper->erpStatistics[$groupCode], [
            'backgrounColor' => '#CC0606',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function appendTotalErpTelGroup()
    {
        return $this->appendRow($this->dataHelper->getBundleTotal($this->dataHelper->erpStatistics, '直效合計'), [
            'backgrounColor' => '#D48005',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function appendHead()
    {
        $this->targetSheet->row($this->getIndex(), $this->getExcelHead());

        return $this;
    }

    protected function setTargetSheet($sheet)
    {
        $this->targetSheet = $sheet;

        return $this;
    }

    protected function appendErpOuttunnel()
    {
        if (array_key_exists(Export::ERP_OUTTUNNEL, $this->dataHelper->erpGroups)) {
            $this->appendByIterateOuttunnel()->appendErpOuttunnelStatistics()->next();
        }
 
        return $this;
    }

    /**
     * 迭代 ERP 群組資料進行 Sheet 欄位添加，
     * 遇到外部通路 group  先跳過不處理
     * 
     * @return $this
     */
    protected function appendByIterateErpTelGroups()
    {
    	foreach ($this->dataHelper->erpGroups as $groupCode => $group) {
            if (Export::ERP_OUTTUNNEL === $groupCode) {
                continue;
            }

            foreach ($group as $agent) {
                $this->dataHelper->updateErpStatistics($agentRow = $this->dataHelper->genAgentRow($agent), $groupCode);
                
                $this->appendEachErpAgent($agentRow)->next();
            }

            $this->appendEachTelGroup($groupCode)->next()->next();
        }

        return $this;
    }

    protected function appendEachErpAgent(array $agentRow)
    {
        return $this->appendRow($agentRow, [
            'backgrounColor' => '#827F7F',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function appendByIterateOuttunnel()
    {
        foreach ($this->dataHelper->erpGroups[Export::ERP_OUTTUNNEL] as $agent) {
            $this->dataHelper->updateErpStatistics($agentRow = $this->dataHelper->genAgentRow($agent), Export::ERP_OUTTUNNEL);
            
            $this->appendEachOuttunnel($agentRow)->next();
        }

        return $this;
    }

    protected function appendEachOuttunnel(array $agentRow)
    {
        return $this->appendRow($agentRow, [
            'backgrounColor' => '#827F7F',
            'fontColor' => '#ffffff'
        ]);
    }

    protected function getCellsRange()
    {
        return "A{$this->getIndex()}:" . self::BORDER_RIGHT_RANGE . "{$this->getIndex()}";
    }

    protected function setSheetBasicStyle()
    {
        $this->targetSheet
            ->setAutoSize(true)
            ->setFontFamily(ExportExcel::FONT_DEFAULT)
            ->setFontSize(12)
            ->setColumnFormat([
                'B' => '@',
                'F' => '#,##0_);(#,##0)',
                'G' => '#,##0_);(#,##0)',
                'H' => '#,##0_);(#,##0)'
            ])
            ->setBorder('A1:' . self::BORDER_RIGHT_RANGE .'1', ExportExcel::BOLDER_DEFAULT)
            ->freezeFirstRow()
        ; 

        $this->targetSheet->cells('A1:' . self::BORDER_RIGHT_RANGE . '1', function ($cells) {
            $cells->setBackground('#000000')->setFontColor('#ffffff')->setAlignment('center');
        });

        return $this;
    }

    public function getExcelHead()
    {
        return [
            '部門',
            '人員代碼',
            '姓名',      
            '會員數',     
            '訂單數',     
            '淨額',      
            '會員均單',    
            '訂單均價',    
            '撥打會員數',   
            '撥打通數',    
            '撥打秒數',
            '工作日' 
        ];
    }
}