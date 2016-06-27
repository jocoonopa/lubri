<?php

namespace App\Export\FV\Sync;

use App\Export\Mould\FVProductMould;

class ProductExport extends FVSyncExport
{
    protected $exceptionObserver = [
        'selfindex@chinghwa.com.tw'  => 'Van',
        'john.cheung@vigasia.com.tw' => 'John',
        'jocoonopa@chinghwa.com.tw'  => '小洪'
    ];

    public function getMould()
    {
        return new FVProductMould;
    }

    public function getType()
    {
        return 'product';
    }

    public function getStartDate()
    {
        return '2016-06-14 00:00:00';
    }
}