SELECT
  COUNT(*)
FROM 
    CallLog WITH(NOLOCK)
WHERE CallLog.StartTime >= '$mdtTime'
ORDER BY CallLog.CampaignCD ASC