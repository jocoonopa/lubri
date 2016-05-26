<?php

namespace App\Export\FVImport;

use App\Export\Mould\FVMemberMould;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;

/**
 * Fetch all member into an export file, limit per select count under 1000
 */
class MemberExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    protected $mould;

    public function handle($export)
    {
        $this->setMould(new FVMemberMould);

        if (!file_exists(storage_path('excel/exports/fvimport/'))) {
            mkdir(storage_path('excel/exports/fvimport/'), 0777);
        }
        
        $fname = storage_path('excel/exports/fvimport/') . 'member_export_' . time() . '.csv';

        $file = fopen($fname, 'w');
        
        $this->proc($export, $file);

        fclose($file);

        $export->getCommend()->comment("\r\n\r\n{$fname}");
        
        return $export->setInfo($fname);
    }

    protected function proc($export, $file)
    {
        $export->getCommend()->comment("\r\nGetting whole data count...");
        $sizeOfAll = $this->getCount($export);
        $export->getCommend()->comment("\r\nStart Writing file");

        $bar = $this->initBar($sizeOfAll, $export);

        $i = 0;
        
        do {
            $members = $this->getMembers($export, $i);

            if (empty($members)) {
                break;
            }
            
            foreach ($members as $member) {
                $appendStr = implode(',', $this->getMould()->getRow($member));
                $appendStr = cb5($appendStr);

                fwrite($file, $appendStr . "\r\n");
            }

            $i = $i + $export->getSize() + 1;

            $bar->advance($export->getSize());
        } while ($i < $sizeOfAll);

        $bar->finish();
    }

    protected function getWhereCondition($export)
    {
        $condStr = '';

        if (null !== $export->getStartAt()) {
            $condStr .= " POS_Member.CRT_TIME >= '{$export->getStartAt()}' AND";
        }

        if (null !== $export->getEndAt()) {
            $condStr .= " POS_Member.CRT_TIME <= '{$export->getEndAt()}' AND";
        }  

        $conStr = substr($condStr, 0, -3);

        return 10 < mb_strlen($conStr) ? "WHERE{$conStr}" : '';
    }

    protected function initBar($sizeOfAll, $export)
    {
        $bar = $export->getOutput()->createProgressBar($sizeOfAll);
        $bar->setRedrawFrequency(1);
        $bar->setFormat('verbose');
        $bar->setOverwrite(true);

        return $bar;
    }

    protected function getCount($export)
    {
        return array_get(Processor::getArrayResult("SELECT COUNT(*) AS _count FROM POS_Member WITH(NOLOCK) {$this->getWhereCondition($export)}"), 0)['_count'];
    }

    protected function getMembers($export, $i)
    {
        $sql = str_replace(
            ['$whereCondition', '$begin', '$end'], 
            [$this->getWhereCondition($export), $i, $i + $export->getSize()], 
            Processor::getStorageSql('FVImport/member.sql')
        );

        //pr($sql);dd();

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