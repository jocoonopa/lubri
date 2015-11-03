SELECT 
    DISTINCT o.OrderNo,
    d.Code,
    d.Name,
    o.OrderDate oDate,
    pl.sDate AS OrderDate,
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
FROM 
    CCS_OrderIndex o
    LEFT JOIN PIS_Document d ON o.DocumentSerNo=d.SerNo
    LEFT JOIN CCS_OrderPays p ON o.SerNo=p.IndexSerNo
    LEFT JOIN POS_PayWay w ON p.PayWaySerNo=w.SerNo
    LEFT JOIN POS_Member m ON o.MemberSerNo=m.SerNo
    LEFT JOIN HRS_Employee e ON o.SalesEmpSerNo=e.SerNo
    LEFT JOIN FAS_Corp c ON e.CorpSerNo=c.SerNo
    LEFT JOIN CCS_OrderPaysLog pl ON pl.IndexSerNo=o.SerNo
    LEFT JOIN HRS_Employee e2 ON pl.KeyInSerNo=e2.SerNo
WHERE 
    o.keyindate < '$date'
    AND SUBSTRING(o.OrderNo,2,8) < '$date'
    AND p.AuthorizeCode LIKE 'E%'
    AND pl.SerNo IN (
        SELECT 
            DISTINCT CCS_OrderPaysLog.SerNo
        FROM 
            CCS_OrderPaysLog With(NoLock)  
            INNER JOIN  HRS_Employee With(NoLock) ON CCS_OrderPaysLog.KeyInSerNo = HRS_Employee.SerNo 
        WHERE
            HRS_Employee.Code IN ('$code') 
            AND CCS_OrderPaysLog.sDate = '$date'
    )
ORDER BY o.OrderNo DESC