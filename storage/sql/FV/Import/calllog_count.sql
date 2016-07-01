SELECT 
    COUNT(*) AS _count
FROM 
    CallLog WITH(NOLOCK)
WHERE CallLog.CustID IN ($codes) AND CallLog.StartTime <= '$mdtTime'
