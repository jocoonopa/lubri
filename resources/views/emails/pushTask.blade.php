<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>{{$task->name}} 推送完成! </title>
</head>
<body>
    <div>
        <p>請至輔翼系統檢視資料是否正確，謝謝!</p>

        <a href="{{env('HOST_IP') . ':'. env('HOST_PORT') . '/flap/pos_member/import_task/' . $task->id }}">前往檢視<b>{{$task->name}}</b>詳情</a>
    </div>
</body>
</html>