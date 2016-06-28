<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>{{$task->name}} 匯入完成! </title>
</head>
<body style="font-family: 微軟正黑體, Roboto,Helvetica,Arial,sans-serif; font-weight: bold;">
    <div>
        <p>{{'Dear ' . $task->user->username . ':'}}</p>

        <p>{{$task->kind->name}}任務<em style="color: #2b6e02;">{{$task->name}}</em> 已經匯入完成，請前往檢視<a href="http://{{env('HOST_IP') . ':'. env('HOST_PORT') . '/flap/pos_member/import_task/' . $task->id }}"><b>{{$task->name}}</b>詳情</a></p>
    </div>
</body>
</html>