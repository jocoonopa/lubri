SELECT 
    COUNT(*)
FROM 
    CampaignCallList WITH(NOLOCK)
WHERE CampaignCallList.modified_at >= '$mdtTime'