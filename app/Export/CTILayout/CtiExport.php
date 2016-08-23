<?php

namespace App\Export\CTILayout;

/**
 * @deprecated [<20160712>] [<No more need to use this class>]
 */
class CtiExport extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    protected $file;
    
    public function getFilename()
    {
        return 'CTILayout_CtiExport';
    }

    /**
     * Gets the value of file.
     *
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the value of file.
     *
     * @param mixed $file the file
     *
     * @return self
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}