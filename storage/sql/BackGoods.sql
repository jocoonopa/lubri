SELECT 
	CCS_OrderIndex.OrderNo, 
	POS_Member.Code, 
	CCS_OrderDivIndex.ReceiveMan, 
	CCS_OrderDivIndex.City, 
	CCS_OrderDivIndex.Town, 
	CCS_OrderDivIndex.Address, 
	CCS_OrderDivIndex.Tel1, 
	CCS_OrderDivIndex.Tel2, 
	CCS_OrderIndex.DeliveryType,
	CCS_OrderIndex.Remark
FROM 
	chinghwa.dbo.CCS_OrderDivIndex CCS_OrderDivIndex, 
	chinghwa.dbo.CCS_OrderIndex CCS_OrderIndex, 
	chinghwa.dbo.POS_Member POS_Member
WHERE 
	CCS_OrderIndex.SerNo = CCS_OrderDivIndex.IndexSerNo 
	AND CCS_OrderIndex.MemberSerNo = POS_Member.SerNo 
	AND ((CCS_OrderDivIndex.IsReturn='Y') 
	AND (CCS_OrderIndex.KeyInDate='$date')
)
ORDER BY CCS_OrderIndex.OrderNo ASC