<?php

namespace App\Import\Flap\POS_Member\ImportActivityTask;

use Input;

class Import extends \App\Import\Flap\POS_Member\Import
{
    protected $fileName;
    protected $destinationPath;

    public static function getNullColumns()
    {
        return [];
    }

    public function getFile()
    {
        $this
            ->setDestinationPath(storage_path('exports') . '/posmember/')
            ->setFileName(md5(time()) . self::FILE_EXT)
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