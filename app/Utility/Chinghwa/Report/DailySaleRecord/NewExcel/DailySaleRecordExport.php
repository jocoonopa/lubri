<?php

namespace App\Utility\Chinghwa\Report\DailySaleRecord\NewExcel;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;

class DailySaleRecordExport extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    protected $date;

    public function getFilename()
    {
        $this->date = Carbon::now()->modify('-1 Days');

        return ExportExcel::DSR_FILENAME . $this->date->format('Ymd');
    }

    public function getDate()
    {
        return $this->date;
    }
}