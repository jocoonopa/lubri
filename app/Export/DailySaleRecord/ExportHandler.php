<?php

namespace App\Export\DailySaleRecord;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;


/**
 * 目前每日業績報表發送為早上六點半以及晚上八點，早上要抓昨天的業績，晚上要抓今日的，
 * 因此用下午兩點"-14 hours" 為分界點, 表示下午兩點以前都是抓"昨天"的業績，之後是抓"今日"的業績
 */
class ExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    const BORDER_RIGHT_RANGE = 'L';
    
    protected $export;
    protected $date;
	protected $rowIndex = 1;
    protected $targetSheet;
    protected $dataHelper;

    public function handle($export)
    {
        $this
            ->setExport($export)
            ->setDate($export->getDate())
        ;

        $export->sheet($this->getSheetToLastOfMonthText(), $this->getSheetToLastOfMonthFunc())
            ->sheet($this->getSheetTodayText(), $this->getSheetTodayFunc())
            ->sheet($this->getSheetTilTodayText(), $this->getSheetTilTodayFunc())
            ->store('xlsx', storage_path('excel/exports'))
        ;

        return $export;
    }

    protected function getSheetToLastOfMonthText()
    {
        return '本月目前業績' . $this->getTodayDate()->modify('first day of this month')->format('Ymd') . '-' . $this->getTodayDate()->modify('last day of this month')->format('Ymd');
    }

    protected function getSheetTodayText()
    {
        return '今日業績' . $this->getTodayDate()->format('Ymd');
    }

    protected function getSheetTilTodayText()
    {
        return '本月累計至今日業績' . $this->getTodayDate()->modify('first day of this month')->format('Ymd') . '-' . $this->getTodayDate()->format('Ymd');
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
            $startDate = $this->getTodayDate()->modify('first day of this month')->format('Ymd');
            $endDate = $this->getTodayDate()->modify('last day of this month')->format('Ymd');

            return $this->toLastOfMonthProc($sheet, $startDate, $endDate);
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
            $this->rowIndex = 1;
            $startDate = $this->getTodayDate()->format('Ymd');
            $endDate = $startDate;

            return $this->todayProc($sheet, $startDate, $endDate);
       };
    }

    protected function getSheetTilTodayFunc()
    {
        return function ($sheet) {
            $this->rowIndex = 1;
            $startDate = $this->getTodayDate()->modify('first day of this month')->format('Ymd');
            $endDate = $this->getTodayDate()->format('Ymd');

            return $this->sheetTilTodayProc($sheet, $startDate, $endDate);
       };
    }

    protected function sheetTilTodayProc($sheet, $startDate, $endDate)
    {
        return $this
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
    }

    protected function toLastOfMonthProc($sheet, $startDate, $endDate)
    {
        return $this
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
    }

    protected function todayProc($sheet, $startDate, $endDate)
    {
        return $this
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
    }

    protected function initDataHelper($startDate, $endDate)
    {
        $this->dataHelper = new ExportHelper($startDate, $endDate);

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

    /**
     * Gets the value of export.
     *
     * @return mixed
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * Sets the value of export.
     *
     * @param mixed $export the export
     *
     * @return self
     */
    protected function setExport($export)
    {
        $this->export = $export;

        return $this;
    }

    /**
     * Gets the value of date.
     *
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the value of date.
     *
     * @param mixed $date the date
     *
     * @return self
     */
    protected function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getCarbonModify()
    {
        return $this->export->getCarbonModify();
    }

    /**
     * Gets the value of todayDate.
     *
     * @return mixed
     */
    public function getTodayDate()
    {
        return Carbon::now()->modify($this->export->getCarbonModify());
    }
}