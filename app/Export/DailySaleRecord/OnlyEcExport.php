<?php

namespace App\Export\DailySaleRecord;

class OnlyEcExport extends Export
{
    protected $to = [
        'darren@chinghwa.com.tw' => '6804張碩'
    ];

    protected $cc = [
        'jeremy@chinghwa.com.tw'    => '6232游加恩',
        'jocoonopa@chinghwa.com.tw' => '6231小閎'
    ];
}