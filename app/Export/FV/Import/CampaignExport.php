<?php

namespace App\Export\FV\Import;

use App\Export\Mould\FVCampaignMould;

class CampaignExport extends FVImportExport
{
    public function getType()
    {
        return 'campaign';
    }

    public function getMould()
    {
        return new FVCampaignMould;
    }
}