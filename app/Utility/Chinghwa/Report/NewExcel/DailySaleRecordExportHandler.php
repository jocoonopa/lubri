<?php

namespace App\Utility\Chinghwa\Report\NewExcel;

use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Report\DailySaleRecord;
use App\Utility\Chinghwa\Helper\Report\DailySaleRecordHelper;
use App\Utility\Chinghwa\Report\NewExcel\DailySaleRecordExport;
use App\Utility\Chinghwa\Report\NewExcel\DataHelper\DailySaleRecordDataHelper;

class DailySaleRecordExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
	const BORDER_RIGHT_RANGE = 'L';

    protected $date;
	protected $rowIndex = 1;
    protected $targetSheet;
    protected $dataHelper;

    public function handle($export)
    {
        $this->date = $export->getDate();

        return $export->sheet('總表', $this->getSheetFunc())->store('xlsx', storage_path('excel/exports'));
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
    protected function getSheetFunc()
    {
        return function ($sheet) {
            $this
                ->setTargetSheet($sheet)
                ->setSheetBasicStyle()
                ->appendHead()->next()
                ->initDataHelper()
                ->appendByIterateErpTelGroups()->prev()        
                ->appendTotalErpTelGroup()->next()
                ->appendErpOuttunnel()
                ->appendByIteratePosGroups()
                ->appendTotalPosGroup()->next()
                ->appendAllSrcTotal()
            ;   
       };
    }

    protected function initDataHelper()
    {
        $this->dataHelper = new DailySaleRecordDataHelper($this->date);

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

    protected function appendTotalPosGroup()
    {
        $this->targetSheet->row($this->getIndex(), $this->dataHelper->getBundleTotal($this->dataHelper->posStatistics, '直營門市合計'));
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#D48005')->setFontColor('#ffffff');
        });

        return $this;
    }

    protected function appendAllSrcTotal()
    {
        $this->targetSheet->row($this->next()->getIndex(), $this->dataHelper->getBundleTotal(array_merge($this->dataHelper->erpStatistics, $this->dataHelper->posStatistics), '公司合計'));
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#088A1E')->setFontColor('#ffffff');
        });

        return $this;
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

    protected function appendEachPosAgent(array $agent, $groupCode)
    {
        $agnetRow = $this->dataHelper->genPosDisplay($agent);
        $this->targetSheet->row($this->getIndex(), $agnetRow);
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#827F7F')->setFontColor('#ffffff');
        });

        $this->dataHelper->updatePosGroupTotal($agnetRow, $groupCode);

        return $this;
    }

    protected function appendEachPosStoreStatistics($groupCode)
    {
        $this->targetSheet->row($this->getIndex(), $this->dataHelper->posStatistics[$groupCode]);
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#CC0606')->setFontColor('#ffffff');
        });

        return $this;
    }

    protected function appendHead()
    {
        $this->targetSheet->row($this->getIndex(), DailySaleRecordHelper::getExcelHead());

        return $this;
    }

    protected function setTargetSheet($sheet)
    {
        $this->targetSheet = $sheet;

        return $this;
    }

    protected function appendErpOuttunnel()
    {
        if (array_key_exists(DailySaleRecord::ERP_OUTTUNNEL, $this->dataHelper->erpGroups)) {
            $this->appendByIterateOuttunnel()->next()->appendErpOuttunnelStatistics()->next();
        }
 
        return $this;
    }

    protected function appendErpOuttunnelStatistics()
    {
        $this->targetSheet->row($this->getIndex(), $this->dataHelper->erpStatistics[DailySaleRecord::ERP_OUTTUNNEL]);
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#CC0606')->setFontColor('#ffffff');
        });

        return $this;
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

    /**
     * 迭代 ERP 群組資料進行 Sheet 欄位添加，
     * 遇到外部通路 group  先跳過不處理
     * 
     * @return $this
     */
    protected function appendByIterateErpTelGroups()
    {
    	foreach ($this->dataHelper->erpGroups as $groupCode => $group) {
            if (DailySaleRecord::ERP_OUTTUNNEL === $groupCode) {
                continue;
            }

            foreach ($group as $agent) {
                $this->appendEachErpAgent($agent, $groupCode)->next();
            }

            $this->appendEachTelGroup($groupCode)->next()->next();
        }

        return $this;
    }

    protected function appendEachErpAgent(array $agent, $groupCode)
    {
        $agentRow = $this->dataHelper->genAgentRow($agent);

        $this->dataHelper->updateErpGroupTotal($agentRow, $groupCode);
        
        $this->targetSheet->row($this->getIndex(), $agentRow);
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#827F7F')->setFontColor('#ffffff');
        });

        return $this;
    }

    protected function appendEachTelGroup($groupCode)
    {
        $this->targetSheet->row($this->getIndex(), $this->dataHelper->erpStatistics[$groupCode]);
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#CC0606')->setFontColor('#ffffff');
        });

        return $this;
    }

    protected function getCellsRange()
    {
    	return "A{$this->getIndex()}:" . self::BORDER_RIGHT_RANGE . "{$this->getIndex()}";
    }

    protected function appendTotalErpTelGroup()
    {
    	$this->targetSheet->row($this->getIndex(), $this->dataHelper->getBundleTotal($this->dataHelper->erpStatistics, '直效合計'));
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#D48005')->setFontColor('#ffffff');
        });

        return $this;
    }

    protected function appendByIterateOuttunnel()
    {
        foreach ($this->dataHelper->erpGroups[DailySaleRecord::ERP_OUTTUNNEL] as $agent) {
            $this->appendEachOuttunnel($agent);
        }

        return $this;
    }

    protected function appendEachOuttunnel(array $agent)
    {
        $agentRow = $this->dataHelper->genAgentRow($agent, $this->dataHelper->ctiCallLog);

        $this->dataHelper->updateErpGroupTotal($agentRow, DailySaleRecord::ERP_OUTTUNNEL);

        $this->targetSheet->row($this->next()->getIndex(), $agentRow);
        $this->targetSheet->cells($this->getCellsRange(), function ($cells) {
            $cells->setBackground('#827F7F')->setFontColor('#ffffff');
        });

        return $this;
    }
}