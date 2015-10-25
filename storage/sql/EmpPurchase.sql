SELECT 
    s.No 單據號碼,
    s.SDate 銷貨日期, 
    e.code 業務人員代號, 
    e.Name 業務人員姓名,
    c.code 部門代號,
    c.Name 部門名稱, 
    su.Code 客戶代號, 
    su.Name 客戶姓名,
    g.code 商品代號, 
    g.name 商品名稱, 
    d.Amount 數量
FROM PIS_SellIndex s
    LEFT JOIN PIS_Document doc ON s.DocumentSerNo=doc.SerNo
    LEFT JOIN PIS_SellDetails d ON s.SerNo=d.SellIndexSerNo
    LEFT JOIN PIS_Goods g ON d.GoodsSerNo=g.SerNo
    LEFT JOIN HRS_Employee e ON d.employeeSerNo=e.SerNo
    LEFT JOIN FAS_Corp c ON d.CorpSerNo=c.SerNo
    LEFT JOIN PIS_HeadShareField hsf ON s.SerNo=hsf.F_SerNo
    LEFT JOIN PIS_Warehouse w ON hsf.WarehouseSerNo=w.SerNo
    LEFT JOIN FAS_Supplier su ON hsf.SupplierSerNo=su.SerNo
WHERE 
    s.sDate BETWEEN '$dateBegin' AND '$dateEnd'
    AND doc.code='0242'
ORDER BY s.SDate,s.No,d.ItemNo