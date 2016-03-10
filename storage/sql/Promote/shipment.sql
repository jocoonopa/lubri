select 
    od.PromoteSerNo,o.OrderNo 訂單單號, 
    o.KeyInDate 出貨日期, 
    o.OrderDate 訂單日期, 
    g.Code 商品代號, 
    g.Name 商品名稱, 
    od.Qty 數量, 
    od.UnitePrice 金額,
    od.SubTotal 金額小計,
    c.Code 部門代號,
    c.Name 部門名稱,
    e.code 業務代號, 
    e.name 業務姓名, 
    m.Code 會員代號,
case when len(p1.Code)>0 then p1.code when len(p2.Code)>0 then p2.code when len(p3.Code)>0 then p3.code when len(p4.Code)>0 then p4.code else null end 促銷代號,
case when len(p1.Code)>0 then p1.Name when len(p2.Code)>0 then p2.Name when len(p3.Code)>0 then p3.Name when len(p4.Code)>0 then p4.Name else null end 促銷名稱,
case when len(p1.Code)>0 then '期間促銷' when len(p2.Code)>0 then '滿額贈' when len(p3.Code)>0 then '商品配套' when len(p4.Code)>0 then '組合促銷' else null end 促銷種類
from CCS_OrderDetails od
left join CCS_OrderIndex o
on od.IndexSerNo=o.SerNo
left join PIS_Goods g
on od.GoodsSerNo=g.SerNo
left join FAS_Corp c
on o.DeptSerNo=c.SerNo
left join HRS_Employee e
on o.SalesEmpSerNo=e.SerNo
left join POS_Member m
on o.MemberSerNo=m.SerNo


/* 1.期間促銷 */
left join CCS_PGoodsGroupI p1
on od.PromoteSerNo=p1.SerNo

/* 2. 滿額贈 */
left join CCS_FullAmountI p2
on od.PromoteSerNo=p2.SerNo

/* 3. 商品配套 */
left join [CCS_CGoodsGroupI] p3
on od.PromoteSerNo=p3.SerNo

/* 4. 組合促銷 */
left join CCS_BCGGPromoteI p4
on od.PromoteSerNo=p4.SerNo

where o.OrderDate between $startDate and $endDate and o.status=1
and p1.code = '$promoteCode' or p2.code = '$promoteCode' or p3.code = '$promoteCode' or p4.code = '$promoteCode'
order by o.OrderNo