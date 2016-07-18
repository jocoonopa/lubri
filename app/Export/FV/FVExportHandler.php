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

    protected function initBar($export)
    {
        $count = min($export->getLimit(), $this->dataHelper->getCount());

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

        fwrite($file, bomstr());
        
        $this->iterateEntitys($bar, $file);

        fclose($file);

        return $this;
    }

    protected function iterateEntitys($bar, $file)
    {
        $bar->start();

        $i = 0;

        $count = $bar->getMaxSteps();

        while ($i < $count) {
            $entitys = $this->dataHelper->fetchEntitys($i);

            if (empty($entitys)) {
                break;
            }
            
            $this->writeRow($file, $entitys);

            $i += $this->dataHelper->getChunkSize();

            $bar->advance($count < $i ? $count - ($i - $this->dataHelper->getChunkSize()) : $this->dataHelper->getChunkSize());
        }
    }

    protected function writeRow($file, $entitys)
    {
        foreach ($entitys as $entity) {
            $appendStr = implode(',', $this->getMould()->getRow($entity));

            fwrite($file, "{$appendStr}\r\n");
        }
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