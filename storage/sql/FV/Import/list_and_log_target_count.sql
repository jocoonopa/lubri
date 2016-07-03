SELECT 
    COUNT(*) AS _count
FROM 
    POS_Member WITH(NOLOCK)
    LEFT JOIN CCS_ShoppingBehaviorBrief WITH(NOLOCK) ON POS_Member.SerNo = CCS_ShoppingBehaviorBrief.MemberSerNoStr
WHERE CCS_ShoppingBehaviorBrief.LastConsumeDate >= '$date' AND POS_Member.SerNo >= '$serno'