<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>Lubri帳號修改通知</title>
</head>
<body>
    <div>
        <p>{{ $user->username }} 於 {{ $user->updated_at->format('Y-m-d H:i:s')}}修改完成</p>
    </div>
</body>
</html>