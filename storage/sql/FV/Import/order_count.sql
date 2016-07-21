SELECT 
    COUNT(*) AS _count
FROM
    CCS_OrderDetails WITH(NOLOCK)
WHERE
CCS_OrderDetails.SerNo >= '$serno'
AND CCS_OrderDetails.SerNo <= '$upserno'