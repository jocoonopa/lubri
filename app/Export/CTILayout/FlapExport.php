<?php

namespace App\Export\CTILayout;

use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use Input;

class FlapExport extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    protected $file;

    public function getFilename()
    {
        return 'CTILayout_FlapExport_' . str_replace(',', '-', Input::get('campaign_cd')) . '_' . time();
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