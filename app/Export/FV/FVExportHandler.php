<?php

namespace App\Export\FV;

abstract class FVExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
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

    protected function initBar($export)
    {
        $count = $export->getLimit() < $this->dataHelper->getCount() ? $export->getLimit() : $this->dataHelper->getCount();

        $bar = $export->getOutput()->createProgressBar($count);
        $bar->setRedrawFrequency(1);
        $bar->setFormat('debug');
        $bar->setOverwrite(true);

        return $bar;
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
        $count = $bar->getMaxSteps();

        fwrite($file, bomstr());
        
        $i = 0;
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

            $bar->advance($count < $i ? $count - ($i - $export->getChunkSize()) : $export->getChunkSize());
        }

        fclose($file);

        return $this;
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