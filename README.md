#Road Map
## 新客獎金

## 輔冀會員PROFILE欄位申請改為佳莉可自行查看

## 門市營業額分析報表目標可由俐穎變動

## Manage Report

各報表寄送對象可透過資料庫控制, 現在每次更換寄送對象都要重新commit, 很麻煩...
之後更改為每個報表會有所謂的指定管理人(原則上寫死), 自行設置報表發送對象, 

並且可以對報表資料設定搜尋條件, 立刻發送等功能

(這個我相信當然最後是由資訊處做，但把它GUI化是考慮到將來萬一我離職了，不可能要接手的人直接從Code 裡面去修改，
所以要做一個比較簡單的使用介面讓後人能接手)

## Refact

- 相關輔助類別重新配置, 現在很多類別都一股腦丟在 App\Utility\Chinghwa, 整個很亂
- 統一架構修正為 Export + ExportHandler, send with QueEvent
- 目前大部分的 ExportHandler都還是太肥大了，有些就算有做最小化也是切的很沒條理，這邊應該要擬出一個固定的設計模式，否則之後不論是要接手或是找人合作開發都會有問題。 

## Route

Route 的定義檔案應該要適度拆解，目前所有的 route 都塞在 routes.php, 難以閱讀

## Cron Job

排程的東西現在都放在 10.72，現在每個發送程式都對應一個cron job, 這樣其實很難管理維護，應該改為只有一支主cron 會去訪問 app，app 接到訪問後從DB 挑出該執行的任務去進行執行

## Issues

目前程式碼大大小小有約 130 個 issues, 希望能降到50個以下

## To Cloud

將服務從本機挪至雲端虛擬主機

## 測試程式碼

測試程式碼目前都沒寫，但這是應該要做的。會很花時間，一般來說寫測試會佔整個軟體開發至少40%的時間，且不容易看出效益，但東西到現在其實也有一定的量了，我希望每周固定能挪出一定時間替系統撰寫測試以利後續維護和延伸開發。這個部分會留待前面所述的幾項事項完成才執行。

## 開發工具

持續加深對目前開發工具的理解和追蹤新釋出的功能，整個專案寫到現在坦白說我也是邊做邊學，以報表發送來說, 對照最開始第一版的和現在所寫的，可以說是天壤之別。未來希望公司更多的庶務能透過此系統解決。

#Reference

- [Laravel Simple ACL](https://gist.github.com/drawmyattention/8cb599ee5dc0af5f4246)

- [Laravel Blade Inject Service && Directive](https://mattstauffer.co/blog/custom-conditionals-with-laravels-blade-directives)

- [Material Guilde](https://github.com/FezVrasta/bootstrap-material-design)