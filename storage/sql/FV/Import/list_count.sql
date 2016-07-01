SELECT 
    COUNT(*) AS _count
FROM 
    CampaignCallList WITH(NOLOCK)
WHERE CampaignCallList.SourceCD IN ($sourcecds) AND CampaignCallList.modified_at <= '$mdtTime'