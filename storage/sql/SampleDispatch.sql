SELECT 
    s.SDate 樣品出貨日期,
    s.No 樣品出貨單號,
    doc.Code 單據代號, 
    doc.Name 單據名稱,
    c.code 部門代號,
    c.Name 部門名稱,
    e.code 樣品出貨人員代號, 
    e.Name 樣品出貨姓名,
    s.NoTaxTotal 未稅金額,
    s.Tax 稅額,
    s.TaxedTotal 含稅金額,
    w.Code 倉別代號, 
    w.Name 倉別名稱, 
    sum(d2.Amount) 數量
FROM PIS_SampleIndex s

LEFT JOIN PIS_Document doc ON s.DocumentSerNo=doc.SerNo
LEFT JOIN (
    SELECT 
        d.SampleIndexSerNo, 
        sum(d.amount) Amount
    FROM PIS_SampleDetails d
    GROUP BY  d.SampleIndexSerNo
) d2 ON s.SerNo=d2.SampleIndexSerNo
LEFT JOIN PIS_HeadShareField hsf ON s.SerNo=hsf.F_SerNo
LEFT JOIN PIS_Warehouse w ON hsf.WarehouseSerNo=w.SerNo
LEFT JOIN HRS_Employee e ON hsf.EmployeeSerNo=e.SerNo
LEFT JOIN FAS_Corp c ON hsf.CorpSerNo=c.SerNo

WHERE s.sDate LIKE '$date'
GROUP BY  
    s.SDate,
    s.No,
    doc.Code, 
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
ORDER BY  
    s.SDate,
    s.No