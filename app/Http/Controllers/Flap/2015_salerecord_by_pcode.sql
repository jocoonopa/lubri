SELECT o2.*, ISNULL(o1.total, 0) - ISNULL(o1.ReturnTotal, 0)  FROM 
(
    SELECT 
        PIS_Goods.Code AS Code, 
        MAX(PIS_Goods.Name) AS Name, 
        SUM(CCS_OrderDetails.SubTotal) AS total,
        SUM(CCS_ReturnGoodsD.SubTotal) AS ReturnTotal
    FROM PIS_Goods WITH(NOLOCK) 
    FULL OUTER JOIN CCS_OrderDetails WITH (NOLOCK) ON PIS_Goods.SerNo = CCS_OrderDetails.GoodsSerNo 
    FULL OUTER JOIN CCS_OrderIndex WITH (NOLOCK) ON CCS_OrderIndex.SerNo = CCS_OrderDetails.IndexSerNo 
        LEFT JOIN CCS_ReturnGoodsI WITH (NOLOCK) ON CCS_OrderIndex.SerNo = CCS_ReturnGoodsI.OrderIndexSerNo
        LEFT JOIN CCS_ReturnGoodsD WITH(NOLOCK) ON CCS_ReturnGoodsI.SerNo = CCS_ReturnGoodsD.IndexSerNo AND CCS_ReturnGoodsD.GoodsSerNo = PIS_Goods.SerNo
    WHERE CCS_OrderIndex.Status=1 AND CCS_OrderIndex.KeyInDate BETWEEN {$startString} AND {$endString} 
    AND PIS_Goods.Code IN('A00546','A00474','A00473','A00492','A00493','A00537','A00544','A00545','A00286','A00461','A00463','A00486','A00497','A00438','A00100','A00482','A00047','A00520','A00422','A00458','A00552','A00553','A00460','A00021','A00284','A00539','A00540','A00541','A00542','A00506','D00119','A00499','A00513','A00519') 
    GROUP BY PIS_Goods.Code  
) o1 
FULL OUTER JOIN (
    SELECT PIS_Goods.Code as Code, PIS_Goods.Name as Name FROM PIS_Goods WHERE PIS_Goods.Code IN('A00546','A00474','A00473','A00492','A00493','A00537','A00544','A00545','A00286','A00461','A00463','A00486','A00497','A00438','A00100','A00482','A00047','A00520','A00422','A00458','A00552','A00553','A00460','A00021','A00284','A00539','A00540','A00541','A00542','A00506','D00119','A00499','A00513','A00519')
) o2 ON o1.Code = o2.Code ORDER BY o2.Code