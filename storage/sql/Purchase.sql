SELECT 
    p.SDate 進貨日期,
    p.No 進貨單號,
    doc.Code 單據代號, 
    doc.Name 單據名稱,
    c.code 部門代號,
    c.Name 部門名稱,
    e.code 進貨人員代號, 
    e.Name  進貨人員姓名,
    p.NoTaxTotal 未稅金額,
    p.Tax 稅額,
    p.TaxedTotal 含稅金額,
    w.Code 倉別代號, 
    w.Name 倉別名稱, 
    sum(d2.Amount) 數量
FROM PIS_QC_PurchaseIndex p
    LEFT JOIN PIS_Document doc ON p.DocumentSerNo=doc.SerNo

    LEFT JOIN (
        SELECT 
            d.QC_PurchaseIndexSerNo, 
            max(d.CorpSerNo) CorpSerNo,
            max(d.EmployeeSerNo) EmployeeSerNo,
            sum(d.Amount) Amount
        FROM PIS_QC_PurchaseDetails d
        GROUP BY  d.QC_PurchaseIndexSerNo
    ) d2 ON p.SerNo=d2.QC_PurchaseIndexSerNo

    LEFT JOIN fas_corp c ON d2.CorpSerNo=c.SerNo
    LEFT JOIN HRS_Employee e ON d2.EmployeeSerNo=e.SerNo
    LEFT JOIN PIS_HeadShareField hsf ON p.SerNo=hsf.F_SerNo
    LEFT JOIN PIS_Warehouse w ON hsf.WarehouseSerNo=w.SerNo

WHERE 
    p.SDate LIKE '$date'
GROUP BY  
    p.SDate,
    p.No,
    doc.Code, 
    doc.Name,
    c.code,
    c.Name, 
    e.code, 
    e.Name,
    p.NoTaxTotal,
    p.Tax,
    p.TaxedTotal,
    w.Code, 
    w.Name
ORDER BY 
    p.SDate,
    p.No