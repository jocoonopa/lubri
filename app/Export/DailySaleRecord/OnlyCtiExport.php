<?php

namespace App\Export\DailySaleRecord;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;

class OnlyCtiExport extends Export
{
    protected $to = [       
        'gavin@chinghwa.com.tw' => '6300何育佳'
    ];

    protected $cc = [
        'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘',
        'jocoonopa@chinghwa.com.tw' => '6231小閎'
    ];

    public function getFilename()
    {
        $this->date = Carbon::now()->modify('-1 Days');

        return ExportExcel::DSR_FILENAME . "{$this->date->format('Ymd')}_cti";
    }
}