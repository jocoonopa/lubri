SELECT 
    FAS_Corp.Name 部門,
    SUM(CCS_OrderIndex.SaleTotal-ISNULL(CCS_ReturnGoodsI.ReturnTotal,0)) AS 本月累計,
    SUM(
        CASE WHEN CCS_OrderIndex.KeyInDate = $today 
        THEN CCS_OrderIndex.SaleTotal-ISNULL(CCS_ReturnGoodsI.ReturnTotal,0) 
        ELSE 0 END
    ) AS 今日業績,
    SUM(
        CASE WHEN CCS_OrderIndex.KeyInDate BETWEEN $weekStart AND $weekEnd
        THEN CCS_OrderIndex.SaleTotal-ISNULL(CCS_ReturnGoodsI.ReturnTotal,0) 
        ELSE 0 END
    ) AS 本周業績       
FROM CCS_OrderIndex WITH(NOLOCK)
LEFT JOIN CCS_ReturnGoodsI WITH(NOLOCK) ON 
    CCS_ReturnGoodsI.MemberSerNo = CCS_OrderIndex.MemberSerNo
    AND CCS_ReturnGoodsI.sDate=CCS_OrderIndex.KeyInDate
LEFT JOIN CCS_CRMFields WITH(NOLOCK) ON CCS_CRMFields.MemberSerNoStr = CCS_OrderIndex.MemberSerNo
LEFT JOIN HRS_Employee WITH(NOLOCK) ON HRS_Employee.SerNo = CCS_CRMFields.ExploitSerNoStr
LEFT JOIN FAS_Corp WITH(NOLOCK) ON FAS_Corp.SerNo = HRS_Employee.CorpSerNo

WHERE $whereCondition
GROUP BY 
    FAS_Corp.Name
ORDER BY
    部門