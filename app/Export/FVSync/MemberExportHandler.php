<?php

namespace App\Export\FVSync;

use App\Export\Mould\FVMemberMould;
use App\Model\Log\FVSyncLog;
use App\Model\Log\FVSyncType;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;

class MemberExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    const START_DATE = '2016-05-31 00:00:00';

    protected $mould;

    /**
     * 1. big5 csv
     * 2. Two style
     *     a. all in one
     *     b. split in each file
     * 
     * @param  [type] $export [description]
     * @return [type]         [description]
     */
    public function handle($export)
    {
        $this->setMould(new FVMemberMould);

        $log = FVSyncLog::latest()->where('type_id', '=', FVSyncType::where('name', '=', 'member')->first()->id)->first();
        
        $mdtTime = !$log ? Carbon::instance(with(new \DateTime(self::START_DATE))) : $log->mrt_time;

        $members = $this->getMembers($mdtTime, $export->getChunkSize());

        $export->setCount(count($members));

        if (!file_exists(storage_path('excel/exports/fvimport/'))) {
            mkdir(storage_path('excel/exports/fvimport/'), 0777);
        }
        
        $fname = storage_path('excel/exports/fvimport/') . 'membersync_export_' . time() . '.csv';

        $file = fopen($fname, 'w');
        
        $this->proc($export, $file);

        fclose($file);

        $export->getCommend()->comment("\r\n\r\n{$fname}");

        $carbon = Carbon::instance(new \DateTime($members[count($members) - 1]['PMDT_TIME']));

        $export->setInfo($fname);

        return $export->setLastMrtTime($carbon);
    }

    protected function proc($export, $file)
    {
        $export->getCommend()->comment("\r\nGetting whole data count...");
        $sizeOfAll = $this->getCount($export);
        $export->getCommend()->comment("\r\nStart Writing file");

        $bar = $this->initBar($sizeOfAll, $export);

        $i = 0;
        
        $this->handleBom($export, $file);

        do {
            $members = $this->getMembers($export, $i);

            if (empty($members)) {
                break;
            }
            
            foreach ($members as $member) {
                $appendStr = implode(',', $this->getMould()->getRow($member));
                $appendStr = true === $export->getIsBig5() ? cb5($appendStr) : $appendStr;

                fwrite($file, $appendStr . "\r\n");
            }

            $i = $i + $export->getChunkSize() + 1;

            $bar->advance($export->getChunkSize());
        } while ($i < $sizeOfAll);

        $bar->finish();
    }

    protected function handleBom($export, $file)
    {
        if (false === $export->getIsBig5() && false === $export->getNobom()) {
            fwrite($file, chr(239) . chr(187) . chr(191));
        }
    }

    protected function getCount($export)
    {
        return array_get(Processor::getArrayResult("SELECT COUNT(*) AS _count FROM POS_Member LEFT JOIN CCS_MemberFlags WITH(NOLOCK) ON POS_Member.SerNo = CCS_MemberFlags.MemberSerNoStr  WITH(NOLOCK) {$this->getWhereCondition($export)}"), 0)['_count'];
    }

    protected function getWhereCondition($export)
    {
        return "WHERE POS_Member.LastModifiedDate >= '{$export->getLastMrtTime()}' OR CCS_MemberFlags.MDT_TIME >= '{$export->getLastMrtTime()}' ";
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

    protected function getMembers($mrtTime, $i)
    {
        $sql = str_replace(
            ['$mrtTime', '$begin', '$end'], 
            [$mrtTime->format('Y-m-d H:i:s'), $i, $i + $export->getChunkSize()], 
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
}