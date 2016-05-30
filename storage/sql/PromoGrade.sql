SELECT
    od.PromoteSerNo,o.OrderNo 訂單單號, 
    o.KeyInDate 出貨日期, 
    o.OrderDate 訂單日期, 
    g.Code 商品代號, 
    g.Name 商品名稱, 
    od.Qty 數量, 
    od.UnitePrice 金額,
    od.SubTotal 金額小計,
    c.Code 部門代號,
    c.Name 部門名稱,
    e.code 業務代號, 
    e.name 業務姓名, 
    m.Code 會員代號,
    CASE WHEN LEN(p1.Code)>0 THEN p1.code 
    WHEN LEN(p2.Code)>0 THEN p2.code 
    WHEN LEN(p3.Code)>0 THEN p3.code 
    WHEN LEN(p4.Code)>0 THEN p4.code ELSE NULL END 促銷代號,
    
    CASE WHEN LEN(p1.Code)>0 THEN p1.Name 
    WHEN LEN(p2.Code)>0 THEN p2.Name
    WHEN LEN(p3.Code)>0 THEN p3.Name 
    WHEN LEN(p4.Code)>0 THEN p4.Name ELSE NULL END 促銷名稱,
    
    CASE WHEN LEN(p1.Code)>0 THEN '期間促銷' 
    WHEN LEN(p2.Code)>0 THEN '滿額贈' 
    WHEN LEN(p3.Code)>0 THEN '商品配套' 
    WHEN LEN(p4.Code)>0 THEN '組合促銷' ELSE NULL END 促銷種類
FROM CCS_OrderDetails od WITH(NOLOCK)
    LEFT JOIN CCS_OrderIndex o ON od.IndexSerNo=o.SerNo
    LEFT JOIN PIS_Goods g ON od.GoodsSerNo=g.SerNo
    LEFT JOIN FAS_Corp c ON o.DeptSerNo=c.SerNo
    LEFT JOIN HRS_Employee e ON o.SalesEmpSerNo=e.SerNo
    LEFT JOIN POS_Member m ON o.MemberSerNo=m.SerNo
    LEFT JOIN CCS_PGoodsGroupI p1 ON od.PromoteSerNo=p1.SerNo
    LEFT JOIN CCS_FullAmountI p2 ON od.PromoteSerNo=p2.SerNo
    LEFT JOIN CCS_CGoodsGroupI p3 ON od.PromoteSerNo=p3.SerNo
    LEFT JOIN CCS_BCGGPromoteI p4 ON od.PromoteSerNo=p4.SerNo
WHERE 
    o.OrderDate between '$dateBegin' AND '$dateEnd' 
    AND o.status=1