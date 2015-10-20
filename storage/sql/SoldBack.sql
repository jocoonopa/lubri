SELECT 
    sr.SDate  銷貨退回日期, 
    sr.No 銷貨退回單號, 
    doc.Code 單據代號, 
    doc.Name 單據名稱, 
    c.Code 部門代號, 
    c.Name 部門名稱, 
    e.Code 銷貨退回人員代號, 
    e.Name 銷貨退回人員姓名,
    sr.NoTaxTotal 未稅金額, 
    sr.Tax 稅額, 
    sr.TaxedTotal 含稅金額,
    w.Code 倉別代號, 
    w.Name 倉別名稱,
    d2.Amount 數量
FROM PIS_Sell_ReturnIndex sr
    LEFT JOIN PIS_Document doc ON sr.DocumentSerNo=doc.SerNo
    LEFT JOIN (
        SELECT 
            d.SellReturnIndexSerNo, 
            max(d.CorpSerNo) CorpSerNo,
            max(d.EmployeeSerNo) EmployeeSerNo,
            sum(d.Amount) Amount
        FROM PIS_Sell_ReturnDetails d
        GROUP BY d.SellReturnIndexSerNo
    ) d2 ON sr.SerNo=d2.SellReturnIndexSerNo

    LEFT JOIN FAS_Corp c ON d2.CorpSerNo=c.SerNo
    LEFT JOIN HRS_Employee e ON d2.EmployeeSerNo=e.SerNo
    LEFT JOIN PIS_HeadShareField hsf ON sr.SerNo=hsf.F_SerNo
    LEFT JOIN PIS_Warehouse w ON hsf.WarehouseSerNo=w.SerNo
WHERE sr.SDate LIKE '$date'