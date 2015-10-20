SELECT 
    s.SDate 銷貨日期,
    s.No 銷貨單號,doc.Code 單據代號, 
    doc.Name 單據名稱,
    c.code 部門代號,
    c.Name 部門名稱,
    e.code 銷貨人員代號, 
    e.Name  銷貨人員姓名,
    s.NoTaxTotal 未稅金額,
    s.Tax 稅額,
    s.TaxedTotal 含稅金額,
    w.Code 倉別代號, 
    w.Name 倉別名稱, 
    sum(d.Amount) 數量
FROM PIS_SellIndex s
LEFT JOIN PIS_Document doc ON s.DocumentSerNo=doc.SerNo
LEFT JOIN PIS_OrderIndex o ON s.No=o.No
LEFT JOIN PIS_SellDetails d ON s.SerNo=d.SellIndexSerNo
LEFT JOIN HRS_Employee e ON d.employeeSerNo=e.SerNo
LEFT JOIN FAS_Corp c ON d.CorpSerNo=c.SerNo
LEFT JOIN PIS_HeadShareField hsf ON s.SerNo=hsf.F_SerNo
LEFT JOIN PIS_Warehouse w ON hsf.WarehouseSerNo=w.SerNo
WHERE s.sDate like '$date'
GROUP BY 
    s.SDate,
    s.No,doc.Code, 
    doc.Name,
    c.code,
    c.Name, 
    e.code, 
    e.Name,
    s.NoTaxTotal,
    s.Tax,
    s.TaxedTotal,
    w.Code, 
    w.Name
ORDER BY s.No,s.SDate