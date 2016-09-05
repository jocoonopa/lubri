<?php

namespace App\Export\DailySaleRecord;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;

class OnlyEcExport extends Export
{
    protected $fileNameTail = 'EC';

    protected $to = [
        'darren@chinghwa.com.tw'       => '6804張碩',
        'angela.chang@chinghwa.com.tw' => '張永萱'
    ];

    protected $cc = [];
}