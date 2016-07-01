<?php

namespace App\Export\FV\Import;

/**
 * Fetch calllogs into an export file
 */
class CalllogExportHandler extends FVImportExportHandler
{
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