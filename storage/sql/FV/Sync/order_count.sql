SELECT 
    COUNT(*) AS _count
FROM
    CCS_OrderDetails WITH(NOLOCK)
WHERE CCS_OrderDetails.MDT_TIME >= '$mdtTime'