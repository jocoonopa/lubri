<?php

namespace App\Http\Controllers\Fix;

use App\Http\Controllers\Controller;
use Mail;
use Maatwebsite\Excel\Facades\Excel;

class DistFlagController extends Controller
{
    // "SQL1": {
    //     SELECT 
    //         Customer.member_id AS 客代,
    //         Customer.cust_cname AS 姓名,
    //         Customer.cust_mobilphone AS 手機,
    //         Customer.cust_tel1 AS 住家電話,
    //         Customer.cust_town_conn + Customer.cust_city_conn + Customer.cust_addconn AS 地址,
    //         distflags.Distflags_4 AS 旗標4,
    //         distflags.Distflags_38 AS 旗標38,
    //         Customer.cust_memo AS 備註1,
    //         Customer.ob_memo AS 備註2,
    //         Customer.fn_memo AS 備註3
    //     FROM Customer
    //         LEFT JOIN distflags ON Customer.cust_id = distflags.MemberCode 
    //     WHERE (
    //         distflags.Distflags_37 IN ('P','Q','R','S','T')
    //         OR distflags.Distflags_38 IN ('P','Q','R','S','T')
    //     )
    //     AND distflags.Distflags_4 = 'N'
    //     AND (
    //         Customer.cust_tel1 IN ('-', '--')
    //         OR Customer.cust_mobilphone IN ('-', '--')
    //         OR Customer.cust_addconn IN ('-', '--')
    //     )
    // },

    // "SQL2": {
    //     SELECT
    //         p.Code AS 會員編號,
    //         p.Name AS 會員姓名,
    //         f.Distflags_31 AS 旗標31,
    //         c.AgentName AS CTI負責人,
    //         c.StatusName AS 狀態,
    //         c.AssignDate AS 指派時間
    //     FROM [192.168.100.66].[chinghwa].[dbo].[POS_Member] AS p
    //         LEFT JOIN [192.168.100.66].[chinghwa].[dbo].[CCS_MemberFlags] AS f ON p.SerNo = f.MemberSerNoStr
    //         LEFT JOIN [192.168.100.3].[CALLCENTER].[dbo].[CampaignCallList] AS c ON c.SourceCD = p.Code
    //     WHERE (
    //         f.[Distflags_37] IN ('P','Q','R','S','T')
    //         OR f.[Distflags_38] IN ('P','Q','R','S','T')
    //     )
    //     AND f.[Distflags_4] = 'Y'
    //     AND f.[Distflags_31] IN ('H','G','F','E')
    //     AND (
    //         p.HomeTel IN ('-', '--')
    //         OR p.CellPhone IN ('-', '--')
    //         OR p.HomeAddress IN ('-', '--')
    //     )
    // },
}