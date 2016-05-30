SELECT
    o1.Ymonth 月份, 
    o1.DeptCode 部門代碼, 
    o1.DeptName 部門, 
    o1.EmpCode 人員代碼, 
    o1.EmpName 姓名, 
    o1.MemberCountD 會員數,
    o1.OrderCountD 訂單數,
    o1.SaleTotal 銷貨, 
    o1.ShipFeeTotal 運費, 
    o1.MustPayTotal 實付,
    o1.UsedBonus 使用紅利,
    o1.PayFees 抵業績,
    ISNULL(r1.ReturnTotal,0) 退貨,
    ISNULL(r1.RTUseBonus,0) 退貨紅利,
    ISNULL(r1.RTPayFees,0) 退貨抵業績, 
    ISNULL(o2.SaleTotal,0)+ISNULL(o2.ShipFeeTotal,0)-ISNULL(o2.UsedBonus,0)-ISNULL(o2.PayFees,0)-ISNULL(r2.ReturnTotal,0)+ISNULL(r2.RTUseBonus,0)+ISNULL(r1.RTPayFees,0) 網路,
    o1.SaleTotal+o1.ShipFeeTotal-o1.UsedBonus-o1.PayFees-ISNULL(r1.ReturnTotal,0)+ISNULL(r1.RTUseBonus,0)+ISNULL(r1.RTPayFees,0) 
    +ISNULL(o2.SaleTotal,0)+ISNULL(o2.ShipFeeTotal,0)-ISNULL(o2.UsedBonus,0)-ISNULL(o2.PayFees,0)-ISNULL(r2.ReturnTotal,0)+ISNULL(r2.RTUseBonus,0)+ISNULL(r1.RTPayFees,0) 淨額
FROM (
    SELECT 
        LEFT(o.keyindate,6) AS Ymonth, 
        c.Code DeptCode, 
        c.Name DeptName, 
        e.Code EmpCode, 
        e.Name EmpName,
        COUNT(distinct o.MemberSerNo) MemberCountD, 
        COUNT(distinct o.OrderNo) OrderCountD, 
        SUM(o.SaleTotal) SaleTotal, 
        SUM(o.ShipFee) ShipFeeTotal, 
        SUM(o.MustPayTotal) MustPayTotal,
        SUM(o.UsedBonus) UsedBonus, 
        SUM(o.PayFees) PayFees
    FROM 
        CCS_OrderIndex o WITH(NOLOCK)
        LEFT JOIN HRS_Employee e WITH(NOLOCK) ON o.SalesEmpSerNo=e.SerNo
        LEFT JOIN FAS_Corp c WITH(NOLOCK) ON e.CorpSerNo=c.Serno
    WHERE 
        o.Status=1 
        AND o.KeyInDate BETWEEN '$startDate' AND '$endDate' 
    GROUP BY LEFT(o.keyindate,6), c.Code, c.Name, e.Code, e.Name
) o1

LEFT JOIN (
    SELECT 
        LEFT(r.sDate,6) AS Ymonth, 
        e.Code AS EmpCode, 
        SUM(ISNULL(r.ReturnTotal,0)) ReturnTotal, 
        SUM(ISNULL(r.PayFees,0)) RTPayFees, 
        SUM(ISNULL(r.UseBonus,0)) RTUseBonus
    FROM CCS_ReturnGoodsI r WITH(NOLOCK)
    LEFT JOIN HRS_Employee e WITH(NOLOCK) ON r.SalesSerNo=e.SerNo
    WHERE r.sDate BETWEEN '$startDate' AND '$endDate'
    GROUP BY LEFT(r.sDate,6), e.Code
) r1 ON o1.Ymonth=r1.Ymonth AND o1.EmpCode=r1.EmpCode

LEFT JOIN (
    SELECT 
        LEFT(o.keyindate,6) AS Ymonth, 
        e1.Code EmpCode, 
        COUNT(distinct o.MemberSerNo) MemberCountD, 
        SUM(o.SaleTotal) SaleTotal, 
        SUM(o.ShipFee) ShipFeeTotal, 
        SUM(o.MustPayTotal) MustPayTotal,
        SUM(o.UsedBonus) UsedBonus, 
        SUM(o.PayFees) PayFees
    FROM 
        CCS_OrderIndex o WITH(NOLOCK)
        LEFT JOIN FAS_Corp c WITH(NOLOCK) ON o.DeptSerNo=c.SerNo
        LEFT JOIN POS_Member m WITH(NOLOCK) ON o.MemberSerNo=m.SerNo
        LEFT JOIN CCS_CRMFields crm WITH(NOLOCK) ON m.SerNo=crm.MemberSerNoStr
        LEFT JOIN hrs_employee e1 WITH(NOLOCK) ON crm.ExploitSerNoStr=e1.SerNo
        LEFT JOIN FAS_Corp c1 WITH(NOLOCK) ON e1.CorpSerNo=c1.SerNo
    WHERE 
        o.Status=1 
        AND o.KeyInDate BETWEEN '$startDate' AND '$endDate'
        AND c.code IN ('CH55110','CH55000')
        AND c1.code NOT IN ('CH55110','CH55000')
    GROUP BY LEFT(o.keyindate,6), e1.Code
) o2 ON o1.Ymonth=o2.Ymonth AND o1.EmpCode=o2.EmpCode

LEFT JOIN (
    SELECT 
        LEFT(r.sDate,6) AS Ymonth, 
        e1.Code AS EmpCode, 
        SUM(ISNULL(r.ReturnTotal,0)) ReturnTotal, 
        SUM(ISNULL(r.PayFees,0)) RTPayFees, 
        SUM(ISNULL(r.UseBonus,0)) RTUseBonus
    FROM 
        CCS_ReturnGoodsI r WITH(NOLOCK)
        LEFT JOIN FAS_Corp c WITH(NOLOCK) ON r.DeptSerNo=c.SerNo
        LEFT JOIN POS_Member m WITH(NOLOCK) ON r.MemberSerNo=m.SerNo
        LEFT JOIN CCS_CRMFields crm WITH(NOLOCK) ON m.SerNo=crm.MemberSerNoStr
        LEFT JOIN hrs_employee e1 WITH(NOLOCK) ON crm.ExploitSerNoStr=e1.SerNo
        LEFT JOIN FAS_Corp c1 WITH(NOLOCK) ON e1.CorpSerNo=c1.SerNo
    WHERE 
        r.sDate BETWEEN '$startDate' AND '$endDate'
        AND c.code IN ('CH55110','CH55000')
        AND c1.code NOT IN ('CH55110','CH55000')
    GROUP BY LEFT(r.sDate,6), e1.Code
) r2 ON o1.Ymonth=r2.Ymonth AND o1.EmpCode=r2.EmpCode

ORDER BY 月份, 部門代碼, 人員代碼
