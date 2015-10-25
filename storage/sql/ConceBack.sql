SELECT 
    sr.No 銷貨單號,
    sr.SDate 銷貨日期, 
    e.code 業務人員代號, 
    e.Name 業務人員姓名,
    c.code 部門代號,
    c.Name 部門名稱, 
    su.Code 廠客代號, 
    su.Name 廠客姓名,
    g.code 商品代號, 
    g.name 商品名稱, 
    d.Amount 數量, 
    d.NoTaxListPrice 稅前定價, 
    d.TaxedListPrice 定價, 
    d.noTaxUnitPrice 單價, 
    d.NoTax 稅前金額, 
    d.TaxedTotal 稅後金額
FROM PIS_Sell_ReturnIndex sr
    LEFT JOIN PIS_Document doc          ON sr.DocumentSerNo=doc.SerNo
    LEFT JOIN PIS_Sell_ReturnDetails d  ON sr.SerNo=d.SellReturnIndexSerNo
    LEFT JOIN PIS_SellIndex s           ON sr.OrderIndexSerNo=s.SerNo
    LEFT JOIN PIS_Goods g               ON d.GoodsSerNo=g.SerNo
    LEFT JOIN HRS_Employee e            ON d.employeeSerNo=e.SerNo
    LEFT JOIN FAS_Corp c                ON d.CorpSerNo=c.SerNo
    LEFT JOIN PIS_HeadShareField hsf    ON sr.SerNo=hsf.F_SerNo
    LEFT JOIN PIS_Warehouse w           ON hsf.WarehouseSerNo=w.SerNo
    LEFT JOIN FAS_Supplier su           ON hsf.SupplierSerNo=su.SerNo
WHERE 
    sr.sDate LIKE '$date' AND 
    doc.code='0256'
ORDER BY sr.SDate,sr.No,d.ItemNo