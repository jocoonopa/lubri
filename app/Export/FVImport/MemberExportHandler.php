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

        if ($sizeOfAll > $export->getLimit()) {
            $sizeOfAll = $export->getLimit();
        }

        $export->getCommend()->comment("\r\nInitial progress bar...");

        $bar = $this->initBar($sizeOfAll, $export);

        $export->getCommend()->comment("\r\nStart Writing file");

        $this->handleBom($export, $file);

        $i = 0;
        while ($i < $sizeOfAll) {
            $members = $this->getMembers($export, $i);

            if (empty($members)) {
                break;
            }
            
            foreach ($members as $member) {
                $appendStr = implode(',', $this->getMould()->getRow($member));
                $appendStr = true === $export->getIsBig5() ? cb5($appendStr) : $appendStr;

                fwrite($file, $appendStr . "\r\n");
            }

            $i = $i + $export->getSize();

            $bar->advance($export->getSize());
        }

        $bar->finish();
    }

    protected function handleBom($export, $file)
    {
        if (false === $export->getIsBig5() && false === $export->getNobom()) {
            fwrite($file, chr(239) . chr(187) . chr(191));
        }
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

        return "WHERE{$condStr} POS_Member.SerNo >= '{$export->getSerno()}' AND POS_Member.SerNo <= '{$export->getUpSerNo()}'";
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