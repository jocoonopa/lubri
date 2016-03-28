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

    <!-- Material Design fonts -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="{!! URL::asset('/assets/css/bootstrap-material-design.min.css') !!}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/0.5.6/css/bootstrap-material-design.min.css.map">
  @yield('css')
</head>
<body id="body" style="background: #000000;">
<div class="container" style="width: 100%; font-size: 80px; color: #ffffff;">
    <table class="table">
        <thead>
            <tr>
                <td>排名</td>
                <td>部門</td>
                <td>姓名</td>
                <td>本月累計</td>
                <td>本周業績</td>
                <td>今日業績</td>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td style="color: red;">{{ $row['排名'] }}</td>
                <td style="color:@if('客戶經營一部' == $row['部門']){{'#B7B7FF'}}@endif @if('客戶經營二部' == $row['部門']){{'yellow'}}@endif @if('客戶經營三部' == $row['部門']) green @endif;">{{ $row['部門'] }}</td>
                <td>{{ $row['姓名'] }}</td>
                <td>{{ number_format($row['本月累計']) }}</td>
                <td>{{ number_format($row['本周業績']) }}</td>
                <td>{{ number_format($row['今日業績']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="{!! URL::asset('/assets/js/jquery.min.js') !!}"></script>
<script src="{!! URL::asset('/assets/js/bootstrap.min.js') !!}"></script>
<script src="{!! URL::asset('/assets/js/material.min.js') !!}"></script>
<script>
$('tbody').find('tr:odd').css('background', '#4E4E4E');

setTimeout(function () {
    window.location.href= '?offset={{ $offset }}';
}, 8000);
</script>
</body>
</html>