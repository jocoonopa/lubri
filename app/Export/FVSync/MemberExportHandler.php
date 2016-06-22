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
    const START_DATE = '2016-05-31 00:00:00';

    protected $mould;
    protected $members;

    /**
     * The main function
     */
    public function handle($export)
    {
        $export->getCommend()->comment("\r\nFVSyncMember is processing, please wait ...");

        if ($this->hasProcessingQue()) {
            $export->getQue()->delete();

            return $export->getCommend()->comment("\r\nThere is another is executing, skip this que!");
        }

        $export
            ->setLastMrtTime($this->getFVSyncQueLastMdtTime())
            ->setCount($this->getMembersCount($export))
            ->setInfo(['file' => $this->genCsvFilePath()])
        ;

        if (0 == $export->getCount()) {
            $export->getQue()->status_code = FVSyncQue::STATUS_SKIP;
            $export->getQue()->save();

            return $export->getCommend()->comment("Nothing need to be imported.");
        }

        $export->getCommend()->comment("Time range start from: {$export->getLastMrtTime()->format('Y-m-d H:i:s')}");
        $export->getCommend()->comment("Has {$export->getCount()} rows");
        
        return $this->proc($export);
    }

    protected function hasProcessingQue()
    {
        $num = FVSyncQue::where('type_id', '=', FVSyncType::where('name', '=', 'member')->first()->id)
            ->whereIn('status_code', [FVSyncQue::STATUS_INIT, FVSyncQue::STATUS_WRITING, FVSyncQue::STATUS_IMPORTING])
            ->count();

        return 1 < $num;
    }

    protected function getFVSyncQueLastMdtTime()
    {
        $que = FVSyncQue::latest()
            ->where('type_id', '=', FVSyncType::where('name', '=', 'member')->first()->id)
            ->whereNotNull('last_modified_at')
            ->first();
        
        return !$que ? Carbon::instance(with(new \DateTime(self::START_DATE))) : $que->last_modified_at->addSecond();
    }

    protected function genCsvFilePath()
    {
        if (!file_exists(storage_path('excel/exports/fvimport/'))) {
            mkdir(storage_path('excel/exports/fvimport/'), 0777);
        }
        
        return storage_path('excel/exports/fvimport/') . 'membersync_export_' . time() . '.csv';
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

        $export->getCommend()->comment("Getting whole data count...");

        $bar = $this->initBar($export);
        $bar->setMessage("Start Writing file {$export->getInfo()['file']}");

        $export->getQue()->status_code = FVSyncQue::STATUS_WRITING;
        $export->getQue()->save();

        //--- 開始執行Query撈取資料寫入匯出檔案 //
        $writeStartAt = microtime(true);

        $this->writeExportFile($export, $bar);

        $export->setSelectCostTime(microtime(true) - $writeStartAt);

        $bar->setMessage('File writing completed');
        $bar->finish();
        //---//
        
        try {
            $export->getQue()->status_code = FVSyncQue::STATUS_IMPORTING;
            $export->getQue()->save();

            //--- 開始呼叫偉特程序，讀取匯出檔案寫入資料庫 //
            $export->getCommend()->comment("\r\nBegin Import File");
            $this->importFile($export);
            $export->getCommend()->comment('Import completed!');
            //---//
            
            // 紀錄最後一筆取得的會員之異動時間，此時間之後會用來當作下次Query 執行的其中一個條件
            $export->getCommend()->comment('Record the last mdt time');
            $export->setLastMrtTime(Carbon::instance(new \DateTime($this->getMembers()[count($this->getMembers()) - 1]['PMDT_TIME'])));
            $export->getCommend()->comment('Record completed!');
        } catch (\Exception $e) {
            $export->getQue()->status_code = FVSyncQue::STATUS_EXCEPTION;
            $export->getQue()->save();

            Log::error($export->getInfo()['file'] . '匯入失敗!');
            $export->getCommend()->comment('Exception happend when doing the import task!');

            throw $e;
        }        

        $export->getQue()->status_code = FVSyncQue::STATUS_COMPLETE;
        $export->getQue()->import_cost_time = $export->getImportCostTime();
        $export->getQue()->select_cost_time = $export->getSelectCostTime();
        $export->getQue()->dest_file        = $export->getInfo()['file'];
        $export->getQue()->last_modified_at = $export->getLastMrtTime();
        $export->getQue()->save();
        
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

                fwrite($file, $appendStr . "\r\n");
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

    public function getSheetCallback(array $members)
    {
        return function ($sheet) use ($members) {
            $sheet->setColumnFormat(['A' => '@','N' => '@']);

            $sheet->appendRow($this->getMould()->getHead()); 

            foreach ($members as $member) {
                $sheet->appendRow($this->getMould()->getRow($member)); 
            }              
        };
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