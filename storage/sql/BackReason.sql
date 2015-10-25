SELECT
    r.No 退貨單據代號, 
    r.sDate 退貨日期, 
    o.OrderNo 原訂單單號, 
    sc.no 來源單號, 
    c.Code 部門代號, 
    c.Name 部門名稱, 
    e.Code 業務代號, 
    e.Name 業務姓名, 
    rr.Code 退貨原因代號, 
    rr.Name 退貨原因, 
    r.Remark 備註
FROM CCS_ReturnGoodsI r

LEFT JOIN PTS_OrderReturnDetails d ON r.SerNo = d.OrderReturnIndexSerNo
LEFT JOIN CCS_OrderIndex o ON r.OrderIndexSerNo = o.SerNo
LEFT JOIN PIS_Document doc ON r.DocumentSerNo = doc.SerNo
LEFT JOIN HRS_Employee e ON o.SalesEmpSerNo = e.SerNo
LEFT JOIN CCS_ReturnGoodsReason rr ON r.ReturnReasonSerNo = rr.SerNo
LEFT JOIN FAS_Corp c ON e.CorpSerNo = c.SerNo
LEFT JOIN PIS_SupCnsnForSaleInd sc ON r.CnsnSerNo = sc.SerNo

WHERE r.sDate LIKE '$date'
ORDER BY r.sDate, r.No