SELECT count(*) AS _count
FROM  
    POS_Member WITH(NOLOCK)
    LEFT JOIN CCS_MemberFlags WITH(NOLOCK)          ON POS_Member.SerNo = CCS_MemberFlags.MemberSerNoStr 
    LEFT JOIN POS_MemberCategory WITH(NOLOCK)       ON POS_Member.MemberClassSerNo = POS_MemberCategory.SerNo
    LEFT JOIN CCS_CRMFields WITH(NOLOCK)            ON POS_Member.SerNo = CCS_CRMFields.MemberSerNoStr
    LEFT JOIN BasicDataDef  WITH(NOLOCK)            ON CCS_CRMFields.Distinction = BasicDataDef.BDSerNo
    LEFT JOIN HRS_Employee WITH(NOLOCK)             ON HRS_Employee.SerNo = CCS_CRMFields.ExploitSerNoStr
    LEFT JOIN FAS_Corp                              ON FAS_Corp.SerNo = HRS_Employee.CorpSerNo 
    LEFT JOIN CCS_ShoppingBehaviorBrief WITH(NOLOCK) ON POS_Member.SerNo = CCS_ShoppingBehaviorBrief.MemberSerNoStr
WHERE POS_Member.LastModifiedDate >= '$mdtTime' 
    OR CCS_MemberFlags.MDT_TIME >= '$mdtTime' 
    OR CCS_ShoppingBehaviorBrief.MDT_TIME >= '$mdtTime'
    OR CCS_CRMFields.MDT_TIME >= '$mdtTime'