<?php

namespace App\Export\FV\Sync;

use App\Export\Mould\FVCampaignMould;

class CampaignExport extends FVSyncExport
{
    protected $exceptionObserver = [
        'selfindex@chinghwa.com.tw'  => 'Van',
        'john.cheung@vigasia.com.tw' => 'John',
        'jocoonopa@chinghwa.com.tw'  => '小洪'
    ];

    public function getMould()
    {
        return new FVCampaignMould;
    }

    public function getType()
    {
        return 'campaign';
    }

    public function getPathEnv()
    {
        return 'FVSYNC_CAMPAIGN_STORAGE_PATH';
    }
}