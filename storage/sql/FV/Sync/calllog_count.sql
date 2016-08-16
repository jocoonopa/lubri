SELECT
  COUNT(*) AS _count
FROM 
    CallLog WITH(NOLOCK)
WHERE CallLog.StartTime >= '$mdtTime' AND CallLog.StartTime <= '$dependLimitTime'