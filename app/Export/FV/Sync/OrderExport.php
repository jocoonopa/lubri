<?php

namespace App\Export\FV\Sync;

use App\Export\Mould\FVOrderMould;

class OrderExport extends FVSyncExport
{
    protected $exceptionObserver = [
        'selfindex@chinghwa.com.tw'  => 'Van',
        'john.cheung@vigasia.com.tw' => 'John',
        'jocoonopa@chinghwa.com.tw'  => '小洪'
    ];

    public function getMould()
    {
        return new FVOrderMould;
    }

    public function getType()
    {
        return 'order';
    }

    public function getStartDate()
    {
        return '2016-06-14 00:00:00';
    }
}