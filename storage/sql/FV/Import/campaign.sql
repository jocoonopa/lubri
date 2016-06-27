SELECT * FROM (
SELECT 
    ROW_NUMBER() over (ORDER BY Campaign.UID) AS lineNum,
    Campaign.* 
FROM Campaign 
WHERE Campaign.StartDate <= '$yesterday' AND Campaign.EndDate >= '$tomorrow'
) AS Campaigns WHERE Campaigns.lineNum > $begin AND Campaigns.lineNum <= $end