<?php

namespace App\Export\FV\Sync;

use App\Export\FV\FVExportHandler;
use App\Export\FV\Sync\Helper\DataHelper;
use App\Export\FV\Sync\Helper\QueHelper;
use Log;
use Mail;

abstract class FVSyncExportHandler extends FVExportHandler
{
    const PROCESS_NAME = 'ProcessNameYouNeedToOverride';

    protected $mould;
    protected $dataHelper;
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
            ->setDataHelper(new DataHelper($export->getType(), $this->queHelper->getLastMrtTime(), $export->getChunkSize()))
        ;

        $export->getCommend()->comment("\r\n|||||||||||| " . self::PROCESS_NAME . " is ready for processing ||||||||||||\r\n");

        if ($this->queHelper->hasProcessingQue()) {
            return $export->getCommend()->comment("\r\nThere's another que is executing now, so scheduler will skip this execution!");
        }

        if (0 === (int) $this->dataHelper->getCount()) {
            $this->queHelper->toSkipStatus();

            return $export->getCommend()->comment("\r\nNothing need to be imported.");
        }

        $export->getCommend()->comment("======================================================\r\nTime range start from: {$this->queHelper->getLastMrtTime()->format('Y-m-d H:i:s')}");
        $export->getCommend()->comment("Has {$this->dataHelper->getCount()} rows\r\n======================================================");
        
        return $this->proc($export);
    }

    protected function genExportFilePath($export)
    {
        if (!file_exists(env('FVSYNC_STORAGE_PATH'))) {
            mkdir(env('FVSYNC_STORAGE_PATH'), 0777, true);
        }
        
        return env('FVSYNC_STORAGE_PATH') . $export->getType() . 'sync_export_' . time() . '.csv';
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
        $bar->setMessage("Start Writing file {$export->getInfo()['file']}");

        try {
            //--- 開始執行Query撈取資料寫入匯出檔案 //
            $this->queHelper->toWritingStatus();
            
            $writeStartAt = microtime(true);

            $this->writeExportFile($export, $bar);

            $this->queHelper->setSelectCostTime(microtime(true) - $writeStartAt);

            $bar->setMessage('File writing completed');
            $bar->finish();
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

            Log::error($export->getInfo()['file'] . '匯入失敗!');
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
     * Write export file by iterate fetch data, which will be used to import in viga db
     * 
     * @param  object $export
     * @return $this
     */
    protected function writeExportFile($export, $bar)
    {
        $file  = fopen($export->getInfo()['file'], 'w');
        $count = $this->dataHelper->getCount();
        $i = 0;
        
        fwrite($file, bomstr());

        while ($i < $count) {
            $entitys = $this->dataHelper->fetchEntitys($export, $i);

            if (empty($entitys)) {
                break;
            }
            
            foreach ($entitys as $entity) {
                $appendStr = implode(',', $this->getMould()->getRow($entity));

                fwrite($file, "{$appendStr}\r\n");
            }

            $i += $export->getChunkSize();

            $bar->advance($count < $export->getChunkSize() ? $count : $export->getChunkSize());
        }

        fclose($file);

        return $this;
    }

    /**
     * Import file to viga db with powerShell and viga .exe
     * 
     * @param  object $export
     * @return boolean      
     */
    protected function importFile($export)
    {
        $output = [];
        
        return exec('"C:\Program Files (x86)\Pivotal\Relation\Relation.exe" /d ' . env('VIG_SYS') . ' /agent CHContactSync ' . basename($export->getInfo()['file']), $output, $status);
    }

    protected function initBar($export)
    {
        $bar = $export->getOutput()->createProgressBar($this->dataHelper->getCount());
        $bar->setRedrawFrequency(1);
        $bar->setFormat('verbose');
        $bar->setOverwrite(true);

        return $bar;
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

    /**
     * Sets the value of dataHelper.
     *
     * @param mixed $dataHelper the data helper
     *
     * @return self
     */
    protected function setDataHelper($dataHelper)
    {
        $this->dataHelper = $dataHelper;

        return $this;
    }

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
}