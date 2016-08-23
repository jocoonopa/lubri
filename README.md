#Reference

- [Laravel Simple ACL](https://gist.github.com/drawmyattention/8cb599ee5dc0af5f4246)

- [Laravel Blade Inject Service && Directive](https://mattstauffer.co/blog/custom-conditionals-with-laravels-blade-directives)

- [Material Guilde](https://github.com/FezVrasta/bootstrap-material-design)

https://select2.github.io/examples.html#tags
http://www.maatwebsite.nl/laravel-excel/docs/blade
http://www.daterangepicker.com/#examples
http://carbon.nesbot.com/docs/

==========================================================

       ~~~  Facade
Provider    ===> Service  <---- Repository <--- [Model, Presneter<---Transformer, Validator, Criteria, Cache]
SomeHelper <===  Service         
                Contract

Can Implement with Que

----------------------------------------------------------------------------------------------------------------------------

#需求

由於同步常常有需要更改排程指標啟動時間以達到資料修正之目的，為了便利客戶即時修正不用等工程師處理，
因此於QueList頁面增加[會員、名單、訂單、Calllog]排程指標時間功能。

#規格
##介面與操作流程

- QueList頁面增加[會員、名單、訂單、Calllog]等排程指標時間欄位 - 0.5HR
- 使用者點擊輸入欄位，跳出時間日期選取Dialog - 0.5HR
- 時間欄位發生變更，觸發非同步儲存動作 - 1HR
- 儲存完成跳出綠色 Toast 提示成功，失敗跳出紅色 Toast提示失敗 - 0.5HR
(至此該Type下次的job啟始時間指標即更新為輸入的值)

2.5HR

##程式邏輯以及動作

- 傳入更新的值{%type%, %datatime%} - 0.5HR
- 若Que為執行中狀態: 更新值於 FVPreSetLMDT 新增/更新 - 0.5HR
- Que完成匯入動作後去FVPreSetLMDT搜尋欲更新之Que之id對應資料 - 0.5hr
    - 若有找到，塞入對應的 LDT - 0.5HR
    - 若無則使用原 LDT - 0.5hr

2.5HR

##關聯資料實體

FVSyncQue
FVPreSetLMDT [id, que_id, ldt]

1HR

#提供價值

- 發生同步錯誤需要從較早時間點重新執行排程時，使用者可自行設置，無須透過工程師

#驗收方式

- 修改尚未執行排程，檢視最後成功執行排程之時間指標有無正確變更
- 修改執行中排程，檢視最後成功執行排程之時間指標有無正確變更
- 多次修改執行中排程，檢視最後成功執行排程之時間指標有無正確變更

#估計工時

6HR * 1.2 = 7.2HR

----------------------------------------------------------------------------------------------------------------------------
#需求

紀錄每次產出的會員|瑛聲名單資料，並且標註是否為 reject ，以供日後可以便利的查詢調閱

#規格
##程式邏輯以及動作

###WHEN

- 撈取輔翼資料寫入檔案時，同時寫入 FVSyncLog 紀錄
- 匯入完成後，讀取 reject 檔案
- 將取得的錯誤資料更新至對應 FVSyncLog record

##關聯資料實體

FVSyncLog [id, cust_id, row, que_id, is_reject, reason]

1HR

#提供價值

#驗收方式

----------------------------------------------------------------------------------------------------------------------------

#需求

由於同步常常出現會員資料不存在的狀況，目前系統沒有一個方便查出究竟該會員資料有無被撈取出來的功能，
因此需要輸入會員編號(不支援多筆)即可查出包含有該會員 Que 的功能

#規格
##介面與操作流程

##程式邏輯以及動作


##WHEN
##GIVEN
##THEN

FVSyncLog: [cid, row, que_id{FVSyncQue.id}]

#提供價值

#驗收方式


--------------------------------------------------------------

#需求

景華使用者可透過 WEB 介面立即執行欲啟動之排程。

#規格
##介面與操作流程

##程式邏輯以及動作
##WHEN
##GIVEN
##THEN

#提供價值

#驗收方式

--------------------------------------------------------------

#需求

景華使用者可在QueList頁面點擊重新執行圖示，讓該次Que直接讀取對應之所屬檔案並呼叫偉特程序執行匯入之動作。

#規格
##介面與操作流程

##程式邏輯以及動作
##WHEN
##GIVEN
##THEN

#提供價值

#驗收方式

--------------------------------------------------------------

#需求

點擊 Que list 頁面的匯出檔案欄位，可以下載欄位內容對應檔案

#規格
##介面與操作流程

##程式邏輯以及動作
##WHEN
##GIVEN
##THEN

#提供價值

#驗收方式

--------------------------------------------------------------