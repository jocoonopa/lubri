SELECT 
    o.OrderNo,
    d.Code,
    d.Name,
    o.OrderDate,
    o.KeyInDate, 
    o.MustPayTotal, 
    o.SaleTotal, 
    m.Code AS mCode, 
    m.Name AS mName, 
    m.HomeTel, 
    m.OfficeTel, 
    m.CellPhone,
    e.Code AS 'eCode', 
    e.Name AS 'eName', 
    c.Code AS cCode, 
    c.Name AS cName, 
    p.CNo, 
    w.Code AS wCode, 
    w.Name AS wName, 
    p.StagesTime, 
    p.AuthorizeCode, 
    p.PayValue 
FROM CCS_OrderIndex o
    LEFT JOIN PIS_Document d ON o.DocumentSerNo=d.SerNo
    LEFT JOIN CCS_OrderPays p ON o.SerNo=p.IndexSerNo
    LEFT JOIN POS_PayWay w ON p.PayWaySerNo=w.SerNo
    LEFT JOIN POS_Member m ON o.MemberSerNo=m.SerNo
    LEFT JOIN HRS_Employee e ON o.SalesEmpSerNo=e.SerNo
    LEFT JOIN FAS_Corp c ON e.CorpSerNo=c.SerNo
WHERE o.KeyInDate BETWEEN '$date' AND '$date' 
    AND c.Code IN ('CH53000','CH54000','CH54100','CH54200') 
    AND w.Code IN ('01','09', '10','11','12','13','15') 
    AND o.Status =1
ORDER BY  o.OrderNo