<?php

namespace App\Export\FV\Sync;

use App\Export\Mould\FVListMould;

class ListExport extends FVSyncExport
{
    protected $exceptionObserver = [
        'selfindex@chinghwa.com.tw'  => 'Van',
        'john.cheung@vigasia.com.tw' => 'John',
        'jocoonopa@chinghwa.com.tw'  => '小洪'
    ];

    public function getMould()
    {
        return new FVListMould;
    }

    public function getType()
    {
        return 'list';
    }

    public function getStartDate()
    {
        return '2016-07-01 00:00:00';
    }
}