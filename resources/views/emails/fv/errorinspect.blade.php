<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>同步排程阻塞通知</title>
    <style>
    tr,td {
        border: 1px solid #000000;
    }
    </style>
</head>
<body style="font-family: 微軟正黑體, Roboto,Helvetica,Arial,sans-serif; font-weight: bold;">
    <div>
        <table>
            <thead>
                <tr>
                    <th>編號</th>
                    <th>類型</th>
                    <th>建立時間</th>
                    <th>狀態</th>
                    <th>建立人</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ques as $que)
                <tr>
                    <td>{{$que->id}}</td>
                    <td>{{$que->type->hname}}</td>
                    <td>{{$que->created_at->format('Y-m-d H:i:s')}}</td>
                    <td>{!!$que->getStatusName()!!}</td>
                    <td>{{$que->creater->username}}</td>
                </tr>
            </tbody>
            @endforeach
        </table>
    </div>
</body>
</html>
