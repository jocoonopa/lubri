SELECT * FROM (
SELECT 
    ROW_NUMBER() over (ORDER BY PIS_Goods.SerNo) AS lineNum,
    PIS_Goods.Name AS 品名,
    PIS_Goods.SpecName AS 規格,
    PIS_Goods.Code AS 商品代碼,
    PIS_Brand.Name AS 品牌名稱,
    PIS_Brand.SerNo AS 品牌主鍵,
    PIS_MiddleBrand.Name AS 中品牌名稱,
    PIS_MiddleBrand.SerNo AS 中品牌主鍵,
    PIS_Unit.Name AS 單位名稱,
    PIS_Unit.SerNo AS 單位主鍵,
    PU2.Name AS 中單位名稱,
    PU2.SerNo AS 中單位主鍵,
    PIS_Goods.MiddleExchangeRate AS 中單位數量,
    PIS_GoodsLargeCategory.Name AS 大分類名稱,
    PIS_GoodsLargeCategory.SerNo AS 大分類主鍵,
    PIS_GoodsMiddleCategory.Name AS 中分類名稱,
    PIS_GoodsMiddleCategory.LargeCategorySerNo AS 中分類連接大分類外鍵,
    PIS_GoodsMiddleCategory.SerNo AS 中分類主鍵,
    PIS_GoodsLittleCategory.Name AS 小分類名稱,
    PIS_GoodsLittleCategory.MiddleCategorySerNo AS 小分類連接中分類外鍵,
    PIS_GoodsLittleCategory.SerNo AS 小分類主鍵,
    PIS_Color.Code AS 類別代碼,
    PIS_Color.Name AS 類別名稱,
    PIS_Goods.TaxedUpsetPrice AS 含稅售價,
    PIS_Goods.UpsetPrice AS 不含稅售價,
    PIS_Goods.GoodsSource AS 商品來源,
    PIS_Goods.GoodsType AS 商品性質,
    PIS_Goods.IsStop AS 是否停用,
    PIS_InitialWarehouseAmount.LastAmount AS 庫存
FROM PIS_Goods WITH(NOLOCK)
    LEFT JOIN PIS_Brand WITH(NOLOCK) ON PIS_Goods.BrandSerNo = PIS_Brand.SerNo
    LEFT JOIN PIS_MiddleBrand WITH(NOLOCK) ON PIS_Goods.MiddleBrandSerNo = PIS_MiddleBrand.SerNo
    LEFT JOIN PIS_Unit WITH(NOLOCK) ON PIS_Goods.UnitSerNo = PIS_Unit.SerNo
    LEFT JOIN PIS_Unit AS PU2 WITH(NOLOCK) ON PIS_Goods.UnitSerNo = PU2.SerNo
    LEFT JOIN PIS_GoodsLargeCategory WITH(NOLOCK) ON PIS_Goods.LargeCategorySerNo = PIS_GoodsLargeCategory.SerNo
    LEFT JOIN PIS_GoodsMiddleCategory WITH(NOLOCK) ON PIS_Goods.MiddleCategorySerNo = PIS_GoodsMiddleCategory.SerNo
    LEFT JOIN PIS_GoodsLittleCategory WITH(NOLOCK) ON PIS_Goods.SmallCategorySerNo = PIS_GoodsLittleCategory.SerNo
    LEFT JOIN PIS_Color WITH(NOLOCK) ON PIS_Goods.ColorSerNo = PIS_Color.SerNo
    LEFT JOIN PIS_InitialWarehouseAmount WITH(NOLOCK) ON PIS_Goods.SerNo = PIS_InitialWarehouseAmount.GoodsSerNo AND PIS_InitialWarehouseAmount.WarehouseSerNo = 'WAREH000000000000000001063'
WHERE 
    PIS_Goods.IsStop = 0
    AND (PIS_Goods.MDT_TIME >= '$mdtTime' OR PIS_InitialWarehouseAmount.MDT_TIME >= '$mdtTime')
) AS Products WHERE Products.lineNum > $begin AND Products.lineNum <= $end















