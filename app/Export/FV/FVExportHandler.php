<?php

namespace App\Export\FV;

abstract class FVExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    const PROCESS_NAME = 'ProcessNameYouNeedToOverride';

    protected $mould;
    protected $dataHelper;

    public function handle($export){}

    /**
     * The process 
     * 
     * @param  object $export
     * @return $this
     */
    abstract protected function proc($export);

    abstract protected function genExportFilePath($export);

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
}