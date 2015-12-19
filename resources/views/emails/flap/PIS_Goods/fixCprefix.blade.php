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
                    <th>原單號</th>
                    <th>新單號</th>
                    <th>修改人員</th>
                    <th>修改時間</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($convertGoodses as $goods)
                <tr>
                    <td>{{ $originGoodses[$goods['SerNo']]}}</td>
                    <td>{{ $goods['Code'] }}</td>
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