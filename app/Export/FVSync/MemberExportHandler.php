<?php

namespace App\Export\FVSync;

use App\Export\Mould\FVMemberMould;
use App\Model\Log\FVSyncQue;
use App\Model\Log\FVSyncType;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;

/**
 * todo:
 * - 根據狀態判斷是否要執行
 */
class MemberExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    const QUE_TYPE            = 'member';
    const START_DATE          = '2016-05-31 00:00:00';
    const FVSYNC_STORAGE_PATH = 'excel/exports/fvsync/' . self::QUE_TYPE . '/';

    protected $mould;
    protected $members;

    /**
     * The main function
     */
    public function handle($export)
    {
        $export->getCommend()->comment("\r\n|||||||||||||||||  FVSyncMember is ready for processing |||||||||||||||||");

        if ($this->hasProcessingQue(self::QUE_TYPE)) {
            return $export->getCommend()->comment("\r\nThere's another que is executing now, so scheduler will skip this execution!");
        }

        $export
            ->setQue($this->createQue($export))
            ->setLastMrtTime($this->getFVSyncQueLastMdtTime(self::QUE_TYPE))
            ->setCount($this->getMembersCount($export))
        ;

        if (0 === (int) $export->getCount()) {
            $export->getQue()->status_code = FVSyncQue::STATUS_SKIP;
            $export->getQue()->save();

            return $export->getCommend()->comment("\r\nNothing need to be imported.");
        }

        $export->getCommend()->comment("======================================================\r\nTime range start from: {$export->getLastMrtTime()->format('Y-m-d H:i:s')}");
        $export->getCommend()->comment("Has {$export->getCount()} rows\r\n======================================================");
        
        return $this->proc($export);
    }

    protected function createQue($export)
    {
        $que = new FVSyncQue;

        $que->status_code = FVSyncQue::STATUS_INIT;
        $que->type_id     = FVSyncType::where('name', '=', 'member')->first()->id;

        $que->save();

        return $que;
    }

    protected function hasProcessingQue($type)
    {
        $num = FVSyncQue::where('type_id', '=', FVSyncType::where('name', '=', $type)->first()->id)
            ->whereIn('status_code', [FVSyncQue::STATUS_WRITING, FVSyncQue::STATUS_IMPORTING])
            ->count();

        return 0 < $num;
    }

    protected function getFVSyncQueLastMdtTime($type)
    {
        $lastQue = FVSyncQue::latest()
            ->where('type_id', '=', FVSyncType::where('name', '=', $type)->first()->id)
            ->whereNotNull('last_modified_at')
            ->first();
        
        return !$lastQue ? Carbon::instance(with(new \DateTime(self::START_DATE))) : $lastQue->last_modified_at->addSecond();
    }

    protected function genExportFilePath()
    {
        if (!file_exists(storage_path(self::FVSYNC_STORAGE_PATH))) {
            mkdir(storage_path(self::FVSYNC_STORAGE_PATH), 0777, true);
        }
        
        return storage_path(self::FVSYNC_STORAGE_PATH) . self::QUE_TYPE . 'sync_export_' . time() . '.csv';
    }

    /**
     * The process 
     * 
     * @param  object $export
     * @return $this
     */
    protected function proc($export)
    {
        // 注入 Mould 物件以方便處理會員資料
        $this->setMould(new FVMemberMould);

        $export->setInfo(['file' => $this->genExportFilePath()]);

        $bar = $this->initBar($export);
        $bar->setMessage("Start Writing file {$export->getInfo()['file']}");

        $export->setQueStatus(FVSyncQue::STATUS_WRITING);

        //--- 開始執行Query撈取資料寫入匯出檔案 //
        $writeStartAt = microtime(true);

        $this->writeExportFile($export, $bar);

        $export->setSelectCostTime(microtime(true) - $writeStartAt);

        $bar->setMessage('File writing completed');
        $bar->finish();
        //---//
        
        try {
            $export->setQueStatus(FVSyncQue::STATUS_IMPORTING);

            //--- 開始呼叫偉特程序，讀取匯出檔案寫入資料庫 //
            $export->getCommend()->comment("\r\n\r\n-----------------------------------------------------------\r\nBegin Import File...");
            $this->importFile($export);
            $export->getCommend()->comment("Import completed!\r\n-----------------------------------------------------------\r\n");
            //---//
            
            // 紀錄最後一筆取得的會員之異動時間，此時間之後會用來當作下次Query 執行的其中一個條件
            $export->getCommend()->comment("-----------------------------------------------------------\r\nRecord the last mdt time");
            $export->setLastMrtTime(Carbon::instance(new \DateTime($this->getMembers()[count($this->getMembers()) - 1]['PMDT_TIME'])));
            $export->getCommend()->comment("Record completed!\r\n-----------------------------------------------------------\r\n");
        } catch (\Exception $e) {
            $export->setQueStatus(FVSyncQue::STATUS_EXCEPTION);

            Log::error($export->getInfo()['file'] . '匯入失敗!');
            $export->getCommend()->comment('Exception happend when doing the import task!');

            throw $e;
        }        

        $export->setQueStatus(FVSyncQue::STATUS_COMPLETE)->configQue();
        $export->getCommend()->comment('All process completed!');

        return $this;
    }

    /**
     * Write export file by iterate fetch data, which will be used to import in viga db
     * 
     * @param  object $export
     * @return $this
     */
    protected function writeExportFile($export, $bar = NULL)
    {
        $file = fopen($export->getInfo()['file'], 'w');
        
        $this->handleBom($export, $file);

        $i = 0;

        while ($i < $export->getCount()) {
            $this->setMembers($this->fetchMembers($export, $i));

            if (empty($this->getMembers())) {
                break;
            }
            
            foreach ($this->getMembers() as $member) {
                $appendStr = implode(',', $this->getMould()->getRow($member));
                $appendStr = true === $export->getIsBig5() ? cb5($appendStr) : $appendStr;

                fwrite($file, "{$appendStr}\r\n");
            }

            $i += $export->getChunkSize();

            $bar->advance($export->getCount() < $export->getChunkSize() ? $export->getCount() : $export->getChunkSize());
        }

        fclose($file);

        return $this;
    }

    /**
     * Import file to viga db with powerShell and viga .exe
     * 
     * @param  object $file
     * @return boolean      
     */
    protected function importFile($file){}

    protected function getMembersCount($export)
    {
        return array_get(Processor::getArrayResult("SELECT COUNT(*) AS _count FROM POS_Member WITH(NOLOCK) WHERE LastModifiedDate >= '{$export->getLastMrtTime()->format('Y-m-d H:i:s')}'"), 0)['_count'];
    }

    protected function initBar($export)
    {
        $bar = $export->getOutput()->createProgressBar($export->getCount());
        $bar->setRedrawFrequency(1);
        $bar->setFormat('verbose');
        $bar->setOverwrite(true);

        return $bar;
    }

    protected function handleBom($export, $file)
    {
        if (false === $export->getIsBig5()) {
            fwrite($file, bomstr());
        }
    }

    protected function fetchMembers($export, $i)
    {
        $sql = str_replace(
            ['$mrtTime', '$begin', '$end'], 
            [$export->getLastMrtTime()->format('Y-m-d H:i:s'), $i, $i + $export->getChunkSize()], 
            Processor::getStorageSql('/FVSync/member.sql')
        );

        return Processor::getArrayResult($sql);
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
     * Gets the value of members.
     *
     * @return mixed
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Sets the value of members.
     *
     * @param mixed $members the members
     *
     * @return self
     */
    protected function setMembers($members)
    {
        $this->members = $members;

        return $this;
    }
}