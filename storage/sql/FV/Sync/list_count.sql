SELECT 
    COUNT(*) AS _count
FROM 
    CampaignCallList WITH(NOLOCK)
WHERE CampaignCallList.modified_at >= '$mdtTime'