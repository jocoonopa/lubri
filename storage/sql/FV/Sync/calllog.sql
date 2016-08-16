SELECT * FROM (
SELECT 
    ROW_NUMBER() over (ORDER BY CallLog.CallID) AS lineNum,
    CallLog.CampaignCD AS 活動代號,
    CallLog.CustID AS 客戶代號,
    CallLog.AgentCD AS 專員代號,
    CallLog.StartTime AS 通話開始時間,
    CallLog.EndTime AS 通話結束時間,
    CallLog.StatusCD AS 狀態代號,
    Status.StatusName AS 狀態內文,
    CallLog.ResultCD AS 通話結果代號,
    StatusResult.ResultName AS 通話結果內文,
    CallLog.Note AS 備註
FROM 
    CallLog WITH(NOLOCK) 
    LEFT JOIN Campaign WITH(NOLOCK) ON Campaign.CampaignCD = CallLog.CampaignCD
    LEFT JOIN Status WITH(NOLOCK) ON Status.StatusCD = CallLog.StatusCD
    LEFT JOIN StatusResult WITH(NOLOCK) ON Status.StatusCD = StatusResult.StatusCD AND StatusResult.ResultCD = CallLog.ResultCD
WHERE CallLog.StartTime >= '$mdtTime' AND CallLog.StartTime <= '$dependLimitTime'
) AS CallLogs WHERE CallLogs.lineNum > $begin AND CallLogs.lineNum <= $end 
ORDER BY CallLogs.活動代號 ASC 