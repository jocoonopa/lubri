SELECT 
	CASE WHEN h1.STOCK_NO IN('S009','S013','S049') THEN '北區' ELSE '南區' END AS 分區,
	h1.STOCK_NO+s.STK_NAME AS 門市, 
	h1.T_SALE, 
	h1.PC_NAME,
	h1.PL業績,
	CASE WHEN h1.業績 > 0 THEN (h1.PL業績/h1.業績)*100 ELSE 0 END AS PL業績佔比,
	h1.nonPL業績,
	CASE WHEN h1.業績 > 0 THEN h1.nonPL業績/h1.業績*100  ELSE 0 END AS nonPL業績佔比,
	h1.業績,
	CASE WHEN h1.業績 > 0 THEN h1.業績/h1.業績*100  ELSE 0 END AS 佔比
FROM 
/* PL */
(SELECT 
	h.STOCK_NO, 
	h.T_SALE,
	c.PC_NAME,
	SUM(i.total) 業績,
	SUM(CASE WHEN p.MK_CODE='G00005' THEN i.total ELSE 0 END) PL業績,
	SUM(CASE WHEN p.MK_CODE='G00005' THEN 0 ELSE i.total END) nonPL業績
FROM HTRH h
LEFT JOIN HTRI i
ON h.TRN_DATE=i.TRN_DATE AND h.STOCK_NO=i.STOCK_NO AND h.TM_NO=i.TM_NO AND h.T_SER_NO=i.T_SER_NO
LEFT JOIN PRODUCT p
ON i.PLU_CODE = p.PLU_CODE
LEFT JOIN CASHMF c
ON h.T_SALE=c.CASH_CODE
WHERE h.TRN_DATE BETWEEN '$startDate' AND '$endDate' AND i.PLU_CODE <>''
AND h.STOCK_NO IN ('S008', 'S009', 'S013', 'S014', 'S017', 'S028', 'S049', 'S051')
GROUP BY h.STOCK_NO, h.T_SALE,c.PC_NAME) h1

LEFT JOIN STKNAME s
ON h1.STOCK_NO=s.STOCK_NO
ORDER BY 分區, h1.STOCK_NO,h1.PC_NAME