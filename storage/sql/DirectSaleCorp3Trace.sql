SELECT
    CONVERT(char(10), GetDate(), 112) Today,
    Max(CCS_OrderIndex.OrderDate) MaxDate,
    PARSENAME(CONVERT(VARCHAR,CAST(COUNT(DISTINCT(POS_Member.Code))AS MONEY),1),2) MemCount,
    PARSENAME(CONVERT(VARCHAR,CAST(4054 AS MONEY),-1),2) AS MemBase,
    CONVERT(DECIMAL(5,2),COUNT(DISTINCT(POS_Member.Code))*1.0/4054*100) 'Rate(%)',
    PARSENAME(CONVERT(VARCHAR,CAST((SUM(CCS_OrderIndex.SaleTotal)-SUM(ISNULL(CCS_ReturnGoodsI.ReturnTotal,0)) )AS MONEY),1),2) NetTtl
FROM
    POS_Member
    JOIN CCS_MemberFlags ON CCS_MemberFlags.MemberSerNoStr=POS_Member.SerNo
    JOIN CCS_OrderIndex ON CCS_OrderIndex.MemberSerNo=POS_Member.SerNo
    LEFT JOIN CCS_ReturnGoodsI ON CCS_ReturnGoodsI.MemberSerNo=POS_Member.SerNo
WHERE 
    CCS_MemberFlags.Distflags_24='A'
    AND CCS_OrderIndex.Status=1
    AND CCS_OrderIndex.OrderDate>=20151201