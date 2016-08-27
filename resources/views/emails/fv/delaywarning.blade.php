<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>延時任務{{$que->id}}錯誤通知</title>
</head>
<body style="font-family: 微軟正黑體, Roboto,Helvetica,Arial,sans-serif; font-weight: bold;">
    <div>
        <p>{{ 'Dear ' . $que->creater->username . ':'}}</p>
        <p>瑛聲資料筆數過多，系統無法執行。請更換條件後再建立同步排程。</p>
        <a href="{{env('PROTOCOL') . '://' . env('HOST_IP') . '/report/ctilayout'}}">前往同步列表</a>
    </div>
</body>
</html>