SELECT * FROM (
    SELECT 
        ROW_NUMBER() over (ORDER BY CampaignCallList.UID) AS lineNum,
        DataSchema.SchemaCD,
        DataSchema.SchemaName,
        CampaignCallList.CampaignCD,
        CampaignCallList.CampaignName,
        Campaign.StartDate,
        Campaign.EndDate,
        CampaignCallList.AgentCD,
        CampaignCallList.AgentName,
        CampaignCallList.TelHistory,
        CampaignCallList.StatusCD,
        CampaignCallList.StatusName,
        CampaignCallList.ResultCD,
        CampaignCallList.ResultName,
        CampaignCallList.SourceCD,
        CampaignCallList.FollowupDate,
        CampaignCallList.DialingTime,
        CampaignCallList.AssignDate,
        CampaignCallList.Data12,
        CampaignCallList.Data20,
        CampaignCallList.Note,
        CampaignCallList.modified_by,
        CampaignCallList.modified_at,
        CampaignCallList.created_by,
        CampaignCallList.created_at
    FROM 
        CampaignCallList WITH(NOLOCK) 
        LEFT JOIN Campaign WITH(NOLOCK) ON CampaignCallList.CampaignCD = Campaign.CampaignCD
        LEFT JOIN DataSchema WITH(NOLOCK) ON Campaign.DefSchemaCD = DataSchema.SchemaCD
    WHERE CampaignCallList.SourceCD IN ($sourcecds) AND CampaignCallList.modified_at <= '$mdtTime'
) AS Lists WHERE Lists.lineNum > $begin AND Lists.lineNum <= $end 
ORDER BY Lists.CampaignCD ASC