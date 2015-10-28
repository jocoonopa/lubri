SELECT
    POS_Member.Code 會員代號,
    POS_Member.Name 會員姓名,
    POS_Member.Sex 性別,
    POS_Member.Birthday 生日,
    POS_Member.IDNo 身份證號,
    POS_Member.HomeTel 連絡電話,
    POS_Member.OfficeTel 公司電話,
    POS_Member.CellPhone 手機號碼,
    POS_Member.HomeAddress_State 縣市,
    POS_Member.HomeAddress_City 區,
    POS_Member.HomeAddress_ZipCode 郵遞區號,
    POS_Member.HomeAddress_Address 地址,
    POS_Member.E_Mail 'e-mail',
    HRS_Employee.Code 開發人代號, 
    HRS_Employee.Name 開發人姓名,
    POS_MemberCategory.Code 會員類別代號,
    POS_MemberCategory.Name 會員類別名稱,
    BasicDataDef.BDCode 區別代號,
    BasicDataDef.BDName 區別名稱,
    CCS_ShoppingBehaviorBrief.FirstTraxAmount 首次購物金額,
    CCS_ShoppingBehaviorBrief.FirstTraxDate 首次購物日,
    CCS_ShoppingBehaviorBrief.LastConsume 最後購物金額,
    CCS_ShoppingBehaviorBrief.LastConsumeDate 最後購物日,
    CCS_ShoppingBehaviorBrief.TotalConsume 累積購物金額,
    POS_Member.TotalBonus 累積紅利點數,
    POS_Member.MemberSerNoI 輔翼會員參數,
    CCS_CRMFields.newCustomerMemo 備註
FROM 
    POS_Member
    LEFT JOIN CCS_MemberFlags           ON POS_Member.SerNo = CCS_MemberFlags.MemberSerNoStr 
    LEFT JOIN POS_MemberCategory        ON POS_Member.MemberClassSerNo = POS_MemberCategory.SerNo
    LEFT JOIN CCS_CRMFields             ON POS_Member.SerNo = CCS_CRMFields.MemberSerNoStr
    LEFT JOIN BasicDataDef              ON CCS_CRMFields.Distinction = BasicDataDef.BDSerNo
    LEFT JOIN HRS_Employee              ON HRS_Employee.SerNo = CCS_CRMFields.ExploitSerNoStr
    LEFT JOIN CCS_ShoppingBehaviorBrief ON POS_Member.SerNo = CCS_ShoppingBehaviorBrief.MemberSerNoStr
WHERE CCS_MemberFlags.DistFlags_37='Q'