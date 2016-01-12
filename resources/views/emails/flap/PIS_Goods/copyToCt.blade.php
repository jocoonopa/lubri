<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>
<body>
    <div>
        <table>
            <thead>
                <tr>
                    <th>原產編</th>
                    <th>新產編</th>
                    <th>修改人員</th>
                    <th>修改時間</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($goodses as $goods)
                <tr>
                    <td>{{ $goods['Code']}}</td>
                    <td>{{ 'CT' . $goods['Code'] }}</td>
                    <td>{{ Auth::User()->username }}</td>
                    <td>{{ date('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>    
        </table>
        
        <hr>
        <p>請知悉，謝謝!</p>
    </div>
</body>
</html>