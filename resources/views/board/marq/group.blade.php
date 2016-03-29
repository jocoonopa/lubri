<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <title>業績排名</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Jocoonopa">
    <meta name="description" content="Lubri - For Nutrimate Emps, much more easy to handle data and tasks">
    <meta name="keywords" content="nutrimate, chinghwa, IT, lubri, flap">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{!! URL::asset('/assets/css/bootstrap.min.css') !!}" >
    <link rel="stylesheet" href="{!! URL::asset('/assets/css/bootstrap.extend.css') !!}">
    <link rel="shortcut icon" type="image/png" href="{!! URL::asset('/assets/image/favicon.png') !!}"/>
    <link rel="apple-touch-icon" href="{!! URL::asset('/assets/image/favicon.png') !!}">
  @yield('css')
</head>
<body id="body" style="background: #000000;">
<div class="container" style="width: 100%; font-size: 70px; color: yellow; font-family: 微軟正黑體;">
    <table class="table" style="text-align: right;">
        <thead>
            <tr>
                <td>部門</td>
                <td>本月累計</td>
                <td>本周業績</td>
                <td>今日業績</td>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td style="color: white;">{{ str_replace(['戶','經','營','部'], '', $row['部門']) }}</td>
                <td style="color: white;">{{ number_format($row['本月累計']) }}</td>
                <td style="color: white;">{{ number_format($row['本周業績']) }}</td>
                <td style="color: white;">{{ number_format($row['今日業績']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="{!! URL::asset('/assets/js/jquery.min.js') !!}"></script>
<script src="{!! URL::asset('/assets/js/bootstrap.min.js') !!}"></script>
<script>
$('tbody').find('tr:odd').css('background', '#4E4E4E');

var interval = {{ Input::get('timeout', 10)*1000 }};

setTimeout(function () {
    window.location.href= '/board/marq?offset=0&timeout={{ Input::get('timeout', 10) }}';
}, 3000 > interval ? 3000 : interval);
</script>
</body>
</html>