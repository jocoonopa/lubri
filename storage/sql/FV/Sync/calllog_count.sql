SELECT
  COUNT(*) AS _count
FROM 
    CallLog WITH(NOLOCK)
LEFT JOIN Campaign WITH(NOLOCK) ON Campaign.CampaignCD = CallLog.CampaignCD
WHERE CallLog.StartTime >= '$mdtTime' AND CallLog.StartTime <= '$dependLimitTime'
AND Campaign.EndDate >= '$mdtTime' AND Campaign.StartDate <= '$dependLimitTime'