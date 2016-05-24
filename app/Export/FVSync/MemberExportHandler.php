<?php

namespace App\Export\FVSync;

use App\Export\Mould\FVMemberMould;
use App\Model\Log\FVSyncLog;
use App\Model\Log\FVSyncType;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;

class MemberExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
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

        $members = $this->getMembers(!$log ? Carbon::instance(with(new \DateTime('2010-01-01 00:00:00'))) : $log->mrt_time, $export->getMax());

        $export->setCount(count($members));

        $info = $export->sheet('export', $this->getSheetCallback($members))
            ->store('csv', storage_path('excel/exports/fvsync'), true);

        $export->setInfo($info);

        $carbon = Carbon::instance(new \DateTime($members[count($members) - 1]['PMDT_TIME']));

        return $export->setLastMrtTime($carbon);
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

    protected function getMembers($mrtTime, $max = 100)
    {
        $sql = str_replace(['$mrtTime', '$max'], [$mrtTime->format('Y-m-d H:i:s'), $max], Processor::getStorageSql('/FVSync/member.sql'));

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