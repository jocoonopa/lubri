<?php

namespace App\Export\FV\Import;

use App\Export\FV\Import\Helper\DataHelper;

/**
 * Fetch lists into an export file
 */
class ListExportHandler extends FVImportExportHandler
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
        $export->getCommend()->comment("Has {$this->dataHelper->getCount()} valid members\r\n======================================================");
        
        return $this->proc($export);
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
        $count = $this->dataHelper->fetchLLTargetCount();
        
        fwrite($file, bomstr());
        
        $i = 0;
        while ($i < $count) {
            $this->dataHelper->updateLLTarget($i);

            $_count = $this->dataHelper->fetchCount();

            $j = 0;
            while ($j < $_count) {
                $entitys = $this->dataHelper->fetchEntitys($export, $j);

                if (empty($entitys)) {
                    break;
                }

                foreach ($entitys as $entity) {
                    $appendStr = implode(',', $this->getMould()->getRow($entity));

                    fwrite($file, "{$appendStr}\r\n");
                }

                $j += $export->getChunkSize();
            }
            
            $i += DataHelper::TARGET_CHUNK_SIZE;

            $bar->advance($count < $i ? $count - ($i - DataHelper::TARGET_CHUNK_SIZE) : DataHelper::TARGET_CHUNK_SIZE);
        }

        fclose($file);

        return $this;
    }
}