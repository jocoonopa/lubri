SELECT 
    RANK() OVER (
        ORDER BY SUM(CCS_OrderIndex.SaleTotal-ISNULL(CCS_ReturnGoodsI.ReturnTotal,0)) DESC,
        SUM(
            CASE WHEN CCS_OrderIndex.KeyInDate = $today THEN CCS_OrderIndex.SaleTotal-ISNULL(CCS_ReturnGoodsI.ReturnTotal,0) 
            ELSE 0 
            END
        ) DESC) AS 排名,
    FAS_Corp.Name 部門,
    HRS_Employee.Name 姓名,
    HRS_Employee.Code Code,
    SUM(CCS_OrderIndex.SaleTotal-ISNULL(CCS_ReturnGoodsI.ReturnTotal,0)) AS 本月累計,
    SUM(
        CASE WHEN CCS_OrderIndex.KeyInDate = $today 
        THEN CCS_OrderIndex.SaleTotal-ISNULL(CCS_ReturnGoodsI.ReturnTotal,0) 
        ELSE 0 END
    ) AS 今日業績
    -- SUM(
    --     CASE WHEN CCS_OrderIndex.KeyInDate BETWEEN $weekStart AND $weekEnd
    --     THEN CCS_OrderIndex.SaleTotal-ISNULL(CCS_ReturnGoodsI.ReturnTotal,0) 
    --     ELSE 0 END
    -- ) AS 本周業績       
FROM CCS_OrderIndex WITH(NOLOCK)
LEFT JOIN CCS_ReturnGoodsI WITH(NOLOCK) ON CCS_ReturnGoodsI.OrderIndexSerNo = CCS_OrderIndex.SerNo
LEFT JOIN CCS_CRMFields WITH(NOLOCK) ON CCS_CRMFields.MemberSerNoStr = CCS_OrderIndex.MemberSerNo
LEFT JOIN HRS_Employee WITH(NOLOCK) ON HRS_Employee.SerNo = CCS_CRMFields.ExploitSerNoStr
LEFT JOIN FAS_Corp WITH(NOLOCK) ON FAS_Corp.SerNo = HRS_Employee.CorpSerNo

WHERE $whereCondition
GROUP BY 
    FAS_Corp.Name,
    HRS_Employee.Name,
    HRS_Employee.Code
ORDER BY 
    本月累計 DESC,
    今日業績 DESC,
    部門,
    姓名