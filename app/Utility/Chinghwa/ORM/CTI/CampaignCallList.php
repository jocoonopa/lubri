<?php

namespace App\Utility\Chinghwa\ORM\CTI;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\iORM;
use Carbon\Carbon;

class CampaignCallList implements iORM
{
    public static function isExist(array $options){}

    public static function first(array $options){}

    public static function find(array $options)
    {
        return Processor::getArrayResult(Processor::table('CampaignCallList')
            ->select('*')
            ->where('AgentCD', '=', array_get($options, 'agentCD'))
            ->where('CampaignCD', '=', array_get($options, 'campaignCD')),
            'Cti'
        );
    }
}