<?php

namespace App\Export\FV\Sync;

use App\Export\Mould\FVCalllogMould;

class CalllogExport extends FVSyncExport
{
    protected $exceptionObserver = [
        'selfindex@chinghwa.com.tw'  => 'Van',
        'john.cheung@vigasia.com.tw' => 'John',
        'jocoonopa@chinghwa.com.tw'  => '小洪'
    ];

    public function getMould()
    {
        return new FVCalllogMould;
    }

    public function getType()
    {
        return 'calllog';
    }

    public function getPathEnv()
    {
        return 'FVSYNC_CALLLOG_STORAGE_PATH';
    }
}