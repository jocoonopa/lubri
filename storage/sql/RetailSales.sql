SELECT 
    CASE WHEN h1.STOCK_NO IN ('S009','S013','S049') THEN '北區' ELSE '南區' END AS 分區,
    h1.STOCK_NO,h1.業績 AS 累計實績,
    h2.業績 AS 去年同期, 
    h3.業績 AS 去年當月,
    h4.PL業績,
    h5.PL業績 去年同期PL,
    h4.毛利, 
    h4.PL毛利,
    h1.單數 本月來客,
    h2.單數 去年同期來客
FROM (
    SELECT 
        h.STOCK_NO, 
        sum(h.trn_amt) 業績,
        sum(CASE WHEN h.trn_amt >0 THEN 1 ELSE 0 END) 單數 
    FROM HTRH h
    WHERE h.TRN_DATE BETWEEN $pszCurrentYear$pszCurrentMonth01 AND $pszCurrentYear$pszTailDate
    GROUP BY h.STOCK_NO
) h1

LEFT JOIN (
    SELECT 
        h.STOCK_NO, 
        sum(h.trn_amt) 業績,
        sum(CASE WHEN h.trn_amt >0 THEN 1 ELSE 0 END) 單數 
    FROM HTRH h
    WHERE h.TRN_DATE BETWEEN $pszPastYear$pszCurrentMonth01 AND $pszPastYear$pszTailDate
    GROUP BY h.STOCK_NO
) h2 ON h1.STOCK_NO=h2.STOCK_NO

LEFT JOIN (
    SELECT 
        h.STOCK_NO, 
        sum(h.trn_amt) 業績,
        sum(CASE WHEN h.trn_amt >0 THEN 1 ELSE 0 END) 單數 
    FROM HTRH h
    WHERE h.TRN_DATE BETWEEN $pszPastYear$pszCurrentMonth01 AND $pszPastYear$pszCurrentMonth$pszPastYearLastDayThisMonth
    GROUP BY h.STOCK_NO
) h3 ON h1.STOCK_NO=h3.STOCK_NO

LEFT JOIN(
    SELECT 
        h.STOCK_NO, 
        sum(i.total) 業績,
        sum(CASE WHEN p.MK_CODE='G00005' THEN i.total ELSE 0 END) PL業績,
        sum(round(i.TOTAL-i.COST_NET*1.05,0)) 毛利,
        sum(CASE WHEN p.MK_CODE='G00005' THEN round(i.total-i.COST_NET*1.05,0) ELSE 0 END) PL毛利
    FROM HTRH h
    LEFT JOIN HTRI i ON 
        h.TRN_DATE=i.TRN_DATE AND 
        h.STOCK_NO=i.STOCK_NO AND 
        h.TM_NO=i.TM_NO AND 
        h.T_SER_NO=i.T_SER_NO
    LEFT JOIN PRODUCT p ON i.PLU_CODE = p.PLU_CODE
    WHERE 
        h.TRN_DATE BETWEEN $pszCurrentYear$pszCurrentMonth01 AND $pszCurrentYear$pszTailDate 
        AND i.PLU_CODE <>''
    GROUP BY h.STOCK_NO
) h4 ON h1.STOCK_NO=h4.STOCK_NO

LEFT JOIN (
    SELECT 
        h.STOCK_NO, 
        sum(i.total) 業績,
        sum(CASE WHEN p.MK_CODE='G00005' THEN i.total ELSE 0 END) PL業績,
        sum(round(i.TOTAL-i.COST_NET*1.05,0)) 毛利,
        sum(CASE WHEN p.MK_CODE='G00005' THEN round(i.total-i.COST_NET*1.05,0) ELSE 0 END) PL毛利
    FROM HTRH h
    LEFT JOIN HTRI i ON 
        h.TRN_DATE=i.TRN_DATE AND 
        h.STOCK_NO=i.STOCK_NO AND 
        h.TM_NO=i.TM_NO AND 
        h.T_SER_NO=i.T_SER_NO
    LEFT JOIN PRODUCT p ON i.PLU_CODE = p.PLU_CODE
    WHERE 
        h.TRN_DATE BETWEEN $pszPastYear$pszCurrentMonth01 AND $pszPastYear$pszTailDate AND 
        i.PLU_CODE <>''
    GROUP BY h.STOCK_NO
) h5 ON h1.STOCK_NO=h5.STOCK_NO

ORDER BY 分區, h1.STOCK_NO