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
        $q = Processor::table('CampaignCallList WITH(NOLOCK)')
            ->select('distinct SourceCD, max(UID)')            
            ->groupBy('SourceCD')
        ;

        $agentCD = array_get($options, 'agentCD');

        is_array($agentCD) ? $q->whereIn('AgentCD', $agentCD) : $q->where('AgentCD', '=', $agentCD);

        if (NULL !== array_get($options, 'campaignCD')) {
            $q->where('CampaignCD', '=', array_get($options, 'campaignCD'));
        }

        if (NULL !== array_get($options, 'assignDate')) {
            $q->where('AssignDate', '>=', array_get($options, 'assignDate') . ' 00:00:00');
        }

        //dd(Processor::toSql($q));
        
        return Processor::getArrayResult($q,'Cti');
    }
}