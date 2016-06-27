SELECT * FROM (
SELECT 
    ROW_NUMBER() over (ORDER BY Campaign.UID) AS lineNum,
    Campaign.* 
FROM Campaign
) AS Campaigns WHERE Campaigns.modified_at >= '$mdtTime'