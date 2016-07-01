SELECT * FROM (
    SELECT 
        ROW_NUMBER() over (ORDER BY CampaignCallList.UID) AS lineNum,
        DataSchema.SchemaCD AS 活動類型代號,
        DataSchema.SchemaName AS 活動類型名稱,
        CampaignCallList.CampaignCD AS 活動名單代號,
        CampaignCallList.CampaignName AS 活動名單名稱,
        Campaign.StartDate AS 活動開始時間,
        Campaign.EndDate AS 活動結束時間,
        CampaignCallList.AgentCD AS 專員代號,
        CampaignCallList.AgentName AS 專員姓名,
        CampaignCallList.TelHistory AS 最後撥打號碼,
        CampaignCallList.StatusCD AS 狀態代碼,
        CampaignCallList.StatusName AS 狀態名稱,
        CampaignCallList.ResultCD AS 撥打結果代瑪,
        CampaignCallList.ResultName AS 撥打結果名稱,
        CampaignCallList.SourceCD AS 客戶代號,
        CampaignCallList.FollowupDate AS 追蹤日期,
        CampaignCallList.DialingTime AS 撥打時間,
        CampaignCallList.AssignDate AS 名單分配日期,
        CampaignCallList.Data12 AS Tag,
        CampaignCallList.Data20 AS 最後通話時間,
        CampaignCallList.Note AS 備註,
        CampaignCallList.modified_by AS 最後更改人員代號,
        CampaignCallList.modified_at AS 最後更改時間,
        CampaignCallList.created_by AS 建立人員代號,
        CampaignCallList.created_at AS 建立時間
    FROM 
        CampaignCallList WITH(NOLOCK) 
        LEFT JOIN Campaign WITH(NOLOCK) ON CampaignCallList.CampaignCD = Campaign.CampaignCD
        LEFT JOIN DataSchema WITH(NOLOCK) ON Campaign.DefSchemaCD = DataSchema.SchemaCD
    WHERE CampaignCallList.modified_at >= '$mdtTime'
) AS Lists WHERE Lists.lineNum > $begin AND Lists.lineNum <= $end 
ORDER BY Lists.活動名單代號 ASC
