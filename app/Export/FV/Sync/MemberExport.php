<?php

namespace App\Export\FV\Sync;

use App\Export\Mould\FVMemberMould;

class MemberExport extends FVSyncExport
{
    protected $exceptionObserver = [
        'selfindex@chinghwa.com.tw'  => 'Van',
        'john.cheung@vigasia.com.tw' => 'John',
        'jocoonopa@chinghwa.com.tw'  => '小洪'
    ];

    public function getMould()
    {
        return new FVMemberMould;
    }

    public function getType()
    {
        return 'member';
    }

    public function getPathEnv()
    {
        return 'FVSYNC_MEMBER_STORAGE_PATH';
    }
}