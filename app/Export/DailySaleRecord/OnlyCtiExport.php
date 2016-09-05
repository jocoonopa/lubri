<?php

namespace App\Export\DailySaleRecord;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;

class OnlyCtiExport extends Export
{
    protected $fileNameTail = 'CTI';

    protected $to = [];

    protected $cc = [
        'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘'
    ];
}