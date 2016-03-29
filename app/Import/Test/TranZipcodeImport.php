<?php

namespace App\Import\Test;

use Input;

class TranZipcodeImport extends \Maatwebsite\Excel\Files\ExcelFile 
{
    const CHUNK_SIZE = 500;

    protected $fileName;
    protected $destinationPath;
    
    public function getFile()
    {
        $this
            ->setDestinationPath(storage_path('exports') . '/test/tran_zipcode/')
            ->setFileName(md5(time()) . '_tran_zipcode')
        ;

        return Input::file('file')->move($this->getDestinationPath(), $this->getFileName()) 
            ? "{$this->getDestinationPath()}{$this->getFileName()}" : NULL;
    }

    public function getFilters()
    {
        return [
            'Maatwebsite\Excel\Filters\ChunkReadFilter',
            'chunk'
        ];
    }

    /**
     * Gets the value of fileName.
     *
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Sets the value of fileName.
     *
     * @param mixed $fileName the file name
     *
     * @return self
     */
    protected function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Gets the value of destinationPath.
     *
     * @return mixed
     */
    public function getDestinationPath()
    {
        return $this->destinationPath;
    }

    /**
     * Sets the value of destinationPath.
     *
     * @param mixed $destinationPath the destination path
     *
     * @return self
     */
    protected function setDestinationPath($destinationPath)
    {
        $this->destinationPath = $destinationPath;

        return $this;
    }
}