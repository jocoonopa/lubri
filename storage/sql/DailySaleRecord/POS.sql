SELECT 
    h.STOCK_NO 門市代號,
    s.STK_NAME 門市名稱,
    h.T_SALE 營業員代號,
    c.PC_NAME 營業員名稱,
    COUNT(DISTINCT(h.T_VIP)) 會員數,
    COUNT(DISTINCT((h.T_TK_H+h.T_TK_NO))) 訂單數,
    SUM(h.TRN_AMT) 金額
FROM 
    HTRH h WITH(NOLOCK)
    LEFT JOIN CASHMF c WITH(NOLOCK) ON h.T_SALE=c.CASH_CODE
    LEFT JOIN STKNAME s WITH(NOLOCK) ON h.STOCK_NO=s.STOCK_NO
WHERE 
    LEN(t_sale) > 0 
    AND h.TRN_DATE BETWEEN '$startDate' AND '$endDate'
GROUP BY 
    h.STOCK_NO,
    s.STK_NAME,
    h.T_SALE,
    c.PC_NAME
ORDER BY 
    h.STOCK_NO,
    s.STK_NAME,
    h.T_SALE,
    c.PC_NAME

