SELECT
    t.SDate 調撥日期, 
    t.No 調撥單號, 
    doc.Code 單據代號, 
    doc.Name 單據名稱, 
    c.Code 部門代號, 
    c.Name 部門名稱,
    e.Code 調撥人代號, 
    e.Name 調撥人姓名, 
    wi.Code 調撥入倉代號, 
    wi.Name 調撥入倉名稱, 
    wo.Code 調撥出倉代號, 
    wo.Name 調撥出倉名稱, 
    d2.Amount 數量, 
    d2.SubTotal 金額
FROM PIS_TransferIndex t
LEFT JOIN PIS_Document doc ON t.DocumentSerNo=doc.SerNo
LEFT JOIN (
    SELECT
        d.TransferIndexSerNo, 
        sum(d.TransferOutAmount) Amount, 
        sum(d.TransferOutAmount*round(g.TaxedListPrice,0)) SubTotal
    FROM PIS_TransferDetails d
    LEFT JOIN PIS_Goods g ON d.GoodsSerNo=g.SerNo
    GROUP BY d.TransferIndexSerNo
) d2 ON t.SerNo=d2.TransferIndexSerNo
LEFT JOIN PIS_HeadShareField hsf ON t.SerNo=hsf.F_SerNo
LEFT JOIN HRS_Employee e ON hsf.EmployeeSerNo=e.SerNo
LEFT JOIN FAS_Corp c ON hsf.CorpSerNo=c.SerNo
LEFT JOIN PIS_Warehouse wo ON t.TransferOutWarehouseSerNo=wo.SerNo
LEFT JOIN PIS_Warehouse wi ON t.TransferInWarehouseSerNo=wi.SerNo

WHERE t.sDate LIKE '$date'
ORDER BY 
    t.SDate,
    t.No