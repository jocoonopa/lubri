SELECT 
    count(*) AS _count
FROM Campaign 
WHERE StartDate <= '$yesterday' AND EndDate >= '$tomorrow'