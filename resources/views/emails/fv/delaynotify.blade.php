<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>延時任務{{$que->id}}完成通知</title>
</head>
<body style="font-family: 微軟正黑體, Roboto,Helvetica,Arial,sans-serif; font-weight: bold;">
    <div>
        <p>{{ 'Dear ' . $que->creater->username . ':'}}</p>
        <p>{{ $que->dest_file }} 於 {{ $que->updated_at->format('Y-m-d H:i:s')}}同步完成</p>
        <a href="{{env('PROTOCOL') . '://' . env('HOST_IP') . '/report/ctilayout'}}">前往同步列表</a>
    </div>
</body>
</html>