SELECT 
    count(*) AS _count
FROM Campaign 
WHERE modified_at >= '$mdtTime'