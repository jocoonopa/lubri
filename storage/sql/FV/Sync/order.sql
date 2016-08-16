SELECT * FROM (
    SELECT 
        ROW_NUMBER() over (ORDER BY CCS_OrderDetails.SerNo) AS lineNum,
        CCS_OrderIndex.SerNo AS IndexSerNo,
        CCS_OrderIndex.OrderNo,
        CCS_OrderIndex.OrderDate,
        CCS_OrderDetails.SerNo,
        POS_Member.SerNo AS MSerNo,
        POS_Member.Code AS MCode,
        CCS_OrderIndex.KeyInDate,
        PIS_Goods.SerNo AS GSerNo,
        PIS_Goods.Code AS GCode,
        PIS_Goods.Name AS GName,
        PIS_Unit.SerNo AS USerNo,
        PIS_Unit.Name AS UName,
        CCS_OrderDetails.Qty,
        CCS_OrderDetails.UnitePrice,
        CCS_OrderDetails.SubTotal,
        CCS_OrderIndex.MustPayTotal,
        (SELECT TOP 1 BonusChanges FROM DCS_BonusLog WITH(NOLOCK) WHERE FormNoStr = CCS_OrderIndex.OrderNo ORDER BY BonusLogSerNo DESC) AS BonusChanges,
        (SELECT TOP 1 DiscountedBonus FROM DCS_BonusLog WITH(NOLOCK) WHERE FormNoStr = CCS_OrderIndex.OrderNo ORDER BY BonusLogSerNo DESC) AS DiscountedBonus,
        (SELECT TOP 1 BonusBefore FROM DCS_BonusLog WITH(NOLOCK) WHERE FormNoStr = CCS_OrderIndex.OrderNo  ORDER BY BonusLogSerNo DESC) AS BonusBefore,
        (SELECT TOP 1 BonusAfter FROM DCS_BonusLog WITH(NOLOCK) WHERE FormNoStr = CCS_OrderIndex.OrderNo  ORDER BY BonusLogSerNo DESC) AS BonusAfter
    FROM
        CCS_OrderDetails WITH(NOLOCK)
        LEFT JOIN CCS_OrderIndex WITH(NOLOCK) ON CCS_OrderIndex.SerNo = CCS_OrderDetails.IndexSerNo
        LEFT JOIN POS_Member WITH(NOLOCK) ON CCS_OrderIndex.MemberSerNo = POS_Member.SerNo
        LEFT JOIN PIS_Goods WITH(NOLOCK) ON PIS_Goods.SerNo = CCS_OrderDetails.GoodsSerNo
        LEFT JOIN PIS_Unit WITH(NOLOCK) ON PIS_Unit.SerNo = PIS_Goods.UnitSerNo
    WHERE CCS_OrderDetails.MDT_TIME >= '$mdtTime' AND CCS_OrderDetails.MDT_TIME <= '$dependLimitTime'
) AS OrderDetails WHERE OrderDetails.lineNum > $begin AND OrderDetails.lineNum <= $end 
