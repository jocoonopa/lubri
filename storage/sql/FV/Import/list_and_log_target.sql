SELECT * FROM (
    SELECT 
        ROW_NUMBER() over (ORDER BY POS_Member.SerNo) AS lineNum,
        POS_Member.Code AS Code
    FROM 
        POS_Member WITH(NOLOCK)
        LEFT JOIN CCS_ShoppingBehaviorBrief WITH(NOLOCK) ON POS_Member.SerNo = CCS_ShoppingBehaviorBrief.MemberSerNoStr
    WHERE CCS_ShoppingBehaviorBrief.LastConsumeDate >= '$date'
) AS Members WHERE Members.lineNum > $begin AND Members.lineNum <= $end 