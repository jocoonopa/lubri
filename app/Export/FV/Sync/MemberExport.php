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

    public function getStartDate()
    {
        return '2016-06-14 00:00:00';
    }
}