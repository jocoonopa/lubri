#Reference

- [Laravel Simple ACL](https://gist.github.com/drawmyattention/8cb599ee5dc0af5f4246)

- [Laravel Blade Inject Service && Directive](https://mattstauffer.co/blog/custom-conditionals-with-laravels-blade-directives)

- [Material Guilde](https://github.com/FezVrasta/bootstrap-material-design)

https://select2.github.io/examples.html#tags
http://www.maatwebsite.nl/laravel-excel/docs/blade
http://www.daterangepicker.com/#examples
http://carbon.nesbot.com/docs/

==========================================================


-- yyyy/mm/dd H:i:s
-- [撥打結果][狀態][備註]@date##########[撥打結果][狀態][備註]@date#####... 

-- [5. 索取、郵寄目錄][2. 客經二部--寵兒名單-不再連絡][abcdefg]##########[][][]

-- SELECT 
-- p1.SourceCD,
-- (
--     SELECT Note + '##########'
--      FROM CallLog p2 WITH(NOLOCK)
--     WHERE p2.CustID = p1.SourceCD
--     ORDER BY p2.CustID
--     FOR XML PATH('')
-- ) AS CallLog 
-- FROM CampaignCallList p1 WITH(NOLOCK) WHERE p1.SourceCD='553576'
-- GROUP BY p1.SourceCD