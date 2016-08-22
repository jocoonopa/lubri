SELECT 
    COUNT(*) AS _count
FROM 
    CampaignCallList WITH(NOLOCK)

LEFT JOIN Campaign ON Campaign.CampaignCD = CampaignCallList.CampaignCD
WHERE CampaignCallList.modified_at >= '$mdtTime' AND CampaignCallList.modified_at <= '$dependLimitTime'
AND Campaign.EndDate <= '$dependLimitTime' AND Campaign.StartDate >= '$mdtTime'