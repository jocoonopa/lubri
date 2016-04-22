SELECT 
    RANK() OVER (ORDER BY SUM(Qry_AgtCall.CallTime) DESC, SUM(Qry_AgtCall.CallCount) DESC) AS 排名
    ,Agent.AgentCD
    ,MAX(Agent.AgentName) 姓名
    ,SUM(CASE WHEN Qry_AgtCall.CallDate='$callDate' THEN Qry_AgtCall.CallCount ELSE 0 END) AS 日通數
    ,SUM(CASE WHEN Qry_AgtCall.CallDate='$callDate' THEN Qry_AgtCall.CallTime ELSE 0 END)/60 AS 日分鐘
    ,SUM(Qry_AgtCall.CallCount) AS 月通數
    ,SUM(Qry_AgtCall.CallTime)/60/60 AS 月時數
FROM (
    SELECT 
        CallLog.AgentCD
        ,MAX(CONVERT(CHAR(10), CallLog.StartTime, 112)) AS CallDate
        ,COUNT(CallLog.CallID) AS CallCount
        ,SUM(DATEDIFF(ss,CallLog.StartTime,CallLog.EndTime)) AS CallTime
    FROM CallLog WITH(NOLOCK)
    WHERE CONVERT(CHAR(10), CallLog.StartTime, 112) BETWEEN '$startDate' AND '$endDate'
    AND CallLog.AgentCD NOT IN ('NTMR02', 'NTMR03', '20050302', 'NTMR06', '20151101', 'P0666', 'P0669', 'P0668', 'P0667', 'CSR01', '20090574')
    GROUP BY CallLog.AgentCD, CONVERT(CHAR(10), CallLog.StartTime, 112)
) AS Qry_AgtCall JOIN Agent WITH(NOLOCK) ON Agent.AgentCD = Qry_AgtCall.AgentCD
GROUP BY Agent.AgentCD
ORDER BY 排名, MAX(Agent.AgentName)