<?php

namespace App\Export\FV\Import;

use App\Export\FV\Import\Helper\CallDataHelper as DataHelper;
use App\Utility\Console\MyProgressBar;

/**
 * Fetch lists into an export file
 *
 * todo: 
 * 
 * 1. sub progress bar
 * 2. Find out why data count would be different if we use diefferent chunksize
 * 
 */
class ListExportHandler extends FVImportExportHandler
{
    protected $inchunk = NULL;

    /**
     * The main function
     */
    public function handle($export)
    {       
        // 注入 Mould 物件以方便處理會員資料
        $this
            ->setMould($export->getMould())
            ->setDataHelper(new DataHelper($export))
        ;

        $export->getCommend()->comment("\r\n|||||||||||| " . $export->getType() . "_import is ready for processing ||||||||||||\r\n");
        $export->getCommend()->comment("Has {$this->dataHelper->getTargetCount()} valid members\r\n======================================================");
        
        return $this->proc($export);
    }

    protected function initBar($export)
    {
        $count = min($export->getLimit(), $this->dataHelper->getTargetCount());
        $bar   = $export->getOutput()->createProgressBar($count);
        
        $bar->setRedrawFrequency(1);
        $bar->setFormat(" [%bar%]t:%current:6s%/%max:-6s% %message:20s% %elapsed:9s%");
        $bar->setOverwrite(true);
        $bar->setMessage('s:0/0 c:0');
        $bar->setBarWidth(25);

        $this->setInchunk($export->getCondition()['inchunk']);

        return $bar;
    }

    protected function iterateEntitys($bar, $file)
    {
        $bar->start();
        $validMemberCount = $bar->getMaxSteps();
        $rowCount         = 0;
        $i                = 0;

        while ($i < $validMemberCount) {
            $chunkSize = min(($validMemberCount - $i), $this->getInchunk());
            $this->dataHelper->updateTarget($i, $chunkSize);
            $this->_iterateEntitys($bar, $file, $chunkSize, $rowCount);

            $i += $this->getInchunk();

            $bar->setCurrent(min($validMemberCount, $i));
        }
    }

    private function _iterateEntitys(&$bar, $file, $chunkCount, &$rowCount)
    {
        $j         = 0;
        $listCount = $this->dataHelper->fetchCount();

        if (0 === $listCount) {
            return;
        }
        
        $perUnit   = floor(($chunkCount/$listCount)*10000)/10000;
        $threshold = 0;
        
        $bar->setMessage("s:{$j}/{$listCount} c:{$rowCount}");

        while ($j < $listCount) {
            $entitys = $this->dataHelper->fetchEntitys($j);

            if (empty($entitys)) {
                break;
            }

            $this->writeRow($file, $entitys);

            $j         += count($entitys);
            $rowCount  += count($entitys);
            $threshold += count($entitys)*$perUnit;

            $bar->setMessage("s:{$j}/{$listCount} c:{$rowCount}");

            if (1 <= $threshold) {
                $bar->advance(floor($threshold));

                $threshold -= floor($threshold);
            }
        }        
    }

    /**
     * Gets the value of inchunk.
     *
     * @return mixed
     */
    public function getInchunk()
    {
        return $this->inchunk;
    }

    /**
     * Sets the value of inchunk.
     *
     * @param mixed $inchunk the inchunk
     *
     * @return self
     */
    protected function setInchunk($inchunk)
    {
        $this->inchunk = $inchunk;

        return $this;
    }
}