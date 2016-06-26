SELECT 
    count(*) AS _count
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