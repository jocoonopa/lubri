<?php

namespace App\Export\DailySaleRecord;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;

class OnlyEcExport extends Export
{
    protected $to = [
        'darren@chinghwa.com.tw' => '6804張碩'
    ];

    protected $cc = [
        'jeremy@chinghwa.com.tw'    => '6232游加恩',
        'jocoonopa@chinghwa.com.tw' => '6231小閎'
    ];

    public function getFilename()
    {
        $this->date = Carbon::now()->modify('-1 Days');

        return ExportExcel::DSR_FILENAME . "{$this->date->format('Ymd')}_ec";
    }
}