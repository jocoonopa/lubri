<?php

namespace App\Export\DailySaleRecord;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;

class OnlyEcExport extends Export
{
    protected $fileNameTail = 'EC';

    protected $to = [
        'darren@chinghwa.com.tw'       => '6804張碩',
        'Merc0918@chinghwa.com.tw'     => '羅偉銘',
        'Tina.lin@chinghwa.com.tw'     => '林玉樺',
        'angela.chang@chinghwa.com.tw' => '張永萱'
    ];

    protected $cc = [
        'jeremy@chinghwa.com.tw'    => '6232游加恩',
        'jocoonopa@chinghwa.com.tw' => '6231小閎'
    ];
}