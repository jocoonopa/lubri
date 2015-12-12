<?php

namespace App\Utility\Chinghwa\Report\NewExcel;

use App\Utility\Chinghwa\ExportExcel;

class DailySaleRecordExport extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    protected $date;

    public function getFilename()
    {
        $this->date = new \DateTime();

        return ExportExcel::DSR_FILENAME . $this->date->format('Ymd');
    }

    public function getDate()
    {
        return $this->date;
    }
}