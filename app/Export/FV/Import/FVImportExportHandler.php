<?php

namespace App\Export\FV\Import;

use App\Export\FV\FVExportHandler;
use App\Export\FV\Import\Helper\DataHelper;

abstract class FVImportExportHandler extends FVExportHandler
{
    /**
     * The main function
     */
    public function handle($export)
    {       
        // 注入 Mould 物件以方便處理會員資料
        $this
            ->setMould($export->getMould())
            ->setDataHelper(new DataHelper($export->getType(), $export->getCondition(), $export->getChunkSize()))
        ;

        $export->getCommend()->comment("\r\n|||||||||||| " . $export->getType() . "_import is ready for processing ||||||||||||\r\n");
        $export->getCommend()->comment("Has {$this->dataHelper->getCount()} rows\r\n======================================================");
        
        return $this->proc($export);
    }

    protected function genExportFilePath($export)
    {
        if (!file_exists(env('FVIMPORT_STORAGE_PATH'))) {
            mkdir(env('FVIMPORT_STORAGE_PATH'), 0777, true);
        }
        
        return env('FVIMPORT_STORAGE_PATH') . $export->getType() . '_export_' . time() . '.csv';
    }

    /**
     * The process 
     * 
     * @param  object $export
     * @return $this
     */
    protected function proc($export)
    {
        $export->setInfo(['file' => $this->genExportFilePath($export)]);
        
        $bar = $this->initBar($export);
        $export->getCommend()->comment("\r\nStart Writing file {$export->getInfo()['file']}");

        try {
            //--- 開始執行Query撈取資料寫入匯出檔案 //            
            $this->writeExportFile($export, $bar);
            $bar->finish();
            $export->getCommend()->comment("\r\n{$export->getInfo()['file']} has been wroted completly.");
            //---//
        } catch (\Exception $e) {
            throw $e;
        }       
        return $this;
    }
}