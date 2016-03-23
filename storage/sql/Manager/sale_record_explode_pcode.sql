SELECT o2.*, MAX(o1.corpName) AS corpName, ISNULL(o1.total, 0) - ISNULL(o1.ReturnTotal, 0) as record FROM 
(
    SELECT 
        PIS_Goods.Code AS Code, 
        MAX(PIS_Goods.Name) AS Name, 
        FAS_Corp.Name AS corpName,
        SUM(CCS_OrderDetails.SubTotal) AS total,
        SUM(CCS_ReturnGoodsD.SubTotal) AS ReturnTotal
    FROM PIS_Goods WITH(NOLOCK) 
    FULL OUTER JOIN CCS_OrderDetails WITH (NOLOCK) ON PIS_Goods.SerNo = CCS_OrderDetails.GoodsSerNo 
    FULL OUTER JOIN CCS_OrderIndex WITH (NOLOCK) ON CCS_OrderIndex.SerNo = CCS_OrderDetails.IndexSerNo 
    LEFT JOIN FAS_Corp WITH(NOLOCK) ON FAS_Corp.SerNo = CCS_OrderIndex.DeptSerNo
    LEFT JOIN CCS_ReturnGoodsI WITH (NOLOCK) ON CCS_OrderIndex.SerNo = CCS_ReturnGoodsI.OrderIndexSerNo
    LEFT JOIN CCS_ReturnGoodsD WITH(NOLOCK) ON CCS_ReturnGoodsI.SerNo = CCS_ReturnGoodsD.IndexSerNo AND CCS_ReturnGoodsD.GoodsSerNo = PIS_Goods.SerNo
    WHERE CCS_OrderIndex.Status=1 AND CCS_OrderIndex.KeyInDate BETWEEN $startString AND $endString
    AND PIS_Goods.Code IN('$codes') 
    GROUP BY PIS_Goods.Code  
) o1 
FULL OUTER JOIN (
    SELECT PIS_Goods.Code as Code, PIS_Goods.Name as Name FROM PIS_Goods WHERE PIS_Goods.Code IN('$codes')
) o2 ON o1.Code = o2.Code ORDER BY o2.Code