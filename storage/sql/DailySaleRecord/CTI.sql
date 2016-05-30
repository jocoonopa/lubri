SELECT 
    LEFT(CONVERT(char(10), starttime, 112), 6) AS 月份,
    RTRIM([AgentCD]) AS 人員代碼,
    COUNT(DISTINCT([CustId])) AS 撥打會員數,
    COUNT(TaskCD) AS 撥打通數, 
    SUM(DATEDIFF(second,StartTime,EndTime)) AS 撥打秒數,
    COUNT(DISTINCT(day(starttime))) AS 工作日
FROM CALLCENTER.dbo.CallLog WITH(NOLOCK)
WHERE 
    custId NOT LIKE 'CT.' 
    AND starttime BETWEEN '$startDate' AND '$endDate'
GROUP BY LEFT(CONVERT(char(10), starttime, 112),6), AgentCD
ORDER BY LEFT(CONVERT(char(10), starttime, 112),6), AgentCD