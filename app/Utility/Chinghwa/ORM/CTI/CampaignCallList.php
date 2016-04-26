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

        return Processor::getArrayResult(self::condition($q, $options),'Cti');
    }

    public static function fetchCtiRes(array $options)
    {
        $q = Processor::table('CampaignCallList WITH(NOLOCK)')
            ->select('DataSchema.SchemaCD,
DataSchema.SchemaName,
CampaignCallList.CampaignCD,CampaignCallList.CampaignName,
Campaign.StartDate,
Campaign.EndDate,
CampaignCallList.AgentCD,
CampaignCallList.AgentName,
CampaignCallList.CustName,CampaignCallList.ID,
CampaignCallList.Tel1,CampaignCallList.Tel2,
CampaignCallList.Tel3,CampaignCallList.TelHistory,
CampaignCallList.StatusCD,
CampaignCallList.StatusName,
CampaignCallList.ResultCD,
CampaignCallList.ResultName,
CampaignCallList.SourceCD,
CampaignCallList.FollowupDate,
CampaignCallList.DialingTime,
CampaignCallList.Payday,
CampaignCallList.AssignDate,
CampaignCallList.Data01,
CampaignCallList.Data02,
CampaignCallList.Data03,
CampaignCallList.Data04,
CampaignCallList.Data05,
CampaignCallList.Data06,
CampaignCallList.Data08,
CampaignCallList.Data09,
CampaignCallList.Data11,
CampaignCallList.Data12,
CampaignCallList.Data15,
CampaignCallList.Data16,
CampaignCallList.Data17,
CampaignCallList.Data20,
CampaignCallList.Note,
CampaignCallList.modified_by,
CampaignCallList.modified_at,
CampaignCallList.created_by,
CampaignCallList.created_at')     
            ->leftJoin('Campaign', 'CampaignCallList.CampaignCD', '=', 'Campaign.CampaignCD')
            ->leftJoin('DataSchema', 'Campaign.DefSchemaCD', '=', 'DataSchema.SchemaCD')       
        ;

        return Processor::getArrayResult(self::condition($q, $options),'Cti');
    }

    protected static function condition($q, array $options)
    {
        $agentCD = array_get($options, 'agentCD');
        $sourceCD = array_get($options, 'sourceCD');
        $campaignCD = array_get($options, 'campaignCD');

        if (!empty($agentCD)) {
            is_array($agentCD) ? $q->whereIn('CampaignCallList.AgentCD', $agentCD) : $q->where('AgentCD', '=', $agentCD);
        }

        if (!empty($sourceCD)) {
            is_array($sourceCD) ? $q->whereIn('CampaignCallList.SourceCD', $sourceCD) : $q->where('SourceCD', '=', $sourceCD);
        }     

        if (!empty($campaignCD)) {
            is_array($campaignCD) ? $q->whereIn('CampaignCallList.CampaignCD', $campaignCD) : $q->where('CampaignCD', '=', $campaignCD);
        }

        if (!empty(array_get($options, 'assignDate'))) {
            $q->where('CampaignCallList.AssignDate', '>=', array_get($options, 'assignDate') . ' 00:00:00');
        }

        //pr(Processor::toSql($q));dd();

        return $q;
    }
}