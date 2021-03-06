SELECT 
	UserID.Code, 
	UserID.Name AS UName, 
	HRS_Employee.Code AS HCode,
	HRS_Employee.SerNo AS HSerNo,
	FAS_Corp.Name AS CName
FROM UserID 
LEFT JOIN HRS_Employee ON HRS_Employee.SerNo = UserID.EmployeeSerNo 
LEFT JOIN FAS_Corp ON FAS_Corp.SerNo = HRS_Employee.CorpSerNo 
WHERE UserID.Expire IS NULL