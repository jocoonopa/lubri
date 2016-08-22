<?php

namespace App\Export\FV\Sync;

use App\Export\FV\FVExportHandler;
use App\Export\FV\Sync\Helper\DataHelper;
use App\Export\FV\Sync\Helper\QueHelper;
use Log;
use Mail;

abstract class FVSyncExportHandler extends FVExportHandler
{
    protected $queHelper;

    /**
     * The main function
     */
    public function handle($export)
    {
        // 注入 Mould 物件以方便處理會員資料
        $this
            ->setMould($export->getMould())
            ->setQueHelper(new QueHelper($export))
            ->setDataHelper($this->createAndGetDataHelper($export))
        ;

        $export->getCommend()->comment("\r\n|||||||||||| " . $export->getType() . "_sync is ready for processing ||||||||||||\r\n");

        if ($this->queHelper->hasProcessingQue()) {
            return $export->getCommend()->comment("\r\nThere's another que is executing now, so scheduler will skip this execution!");
        }

        if (0 === (int) $this->dataHelper->getCount()) {
            $this->queHelper->toSkipStatus();

            return $export->getCommend()->comment("\r\nNothing need to be handled with.");
        }

        $export->getCommend()->comment("======================================================\r\nTime range start from: {$this->queHelper->getLastMrtTime()->format('Y-m-d H:i:s')}");
        $export->getCommend()->comment("Has {$this->dataHelper->getCount()} rows\r\n======================================================");
        
        return $this->proc($export);
    }

    protected function createAndGetDataHelper($export)
    {
        return new DataHelper(
            $export->getType(), 
            $this->queHelper->getLastMrtTime(), 
            $export->getChunkSize(), 
            $this->queHelper->getDependLimitTime()
        );
    }

    abstract protected function genExportFilePath($export);

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
        $export->getCommend()->comment("Start Writing file {$export->getInfo()['file']}");

        try {
            //--- 開始執行Query撈取資料寫入匯出檔案 //
            $this->queHelper->toWritingStatus();
            
            $writeStartAt = microtime(true);

            $this->writeExportFile($export, $bar);

            $this->queHelper->setSelectCostTime(microtime(true) - $writeStartAt);

            $bar->finish();
            $export->getCommend()->comment("\r\nFile writing completed");
            //---//
            $this->queHelper->toImportingStatus();

            //--- 開始呼叫偉特程序，讀取匯出檔案寫入資料庫 //
            $export->getCommend()->comment("\r\n\r\n-----------------------------------------------------------\r\nBegin Import File...");
            
            $importStartAt = microtime(true);
            $this->importFile($export);
            $this->queHelper->setImportCostTime(microtime(true) - $importStartAt);    

            $export->getCommend()->comment("Import completed!\r\n-----------------------------------------------------------\r\n");
            //---//
        } catch (\Exception $e) {
            $this->queHelper->toErrorStatus();

            Log::error($export->getInfo()['file'] . '匯入失敗!' . $e->getMessage());
            $this->mail($export, $e);
            
            $export->getCommend()->comment('Exception happend when doing the import task!');

            throw $e;
        }        

        $this->queHelper->toCompleteStatus();
        $export->getCommend()->comment('All process completed!');

        return $this;
    }

    public function mail($export, $e)
    {
        return Mail::raw(__CLASS__ . '###' . $e->getMessage(), function ($m) use ($export) {
            $m->to($export->getExceptionObserver())->subject('FVSyncQue_Error_' . date('Y-m-d H:i:s'));
        });
    }

    /**
     * Import file to viga db with powerShell and viga .exe
     * 
     * @param  object $export
     * @return boolean      
     */
    abstract protected function importFile($export);

    /**
     * Sets the value of queHelper.
     *
     * @param mixed $queHelper the que helper
     *
     * @return self
     */
    protected function setQueHelper($queHelper)
    {
        $this->queHelper = $queHelper;

        return $this;
    }

    public function initQueHelper($queHelper)
    {
        return $this->setQueHelper($queHelper);
    }
}