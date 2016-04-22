<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <title>撥打排名</title>
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
    <style>
      tbody tr:nth-child(odd) {
        background: #4E4E4E;
    }
    </style>
</head>
<body id="body" style="background: #000000;">
<div class="container" style="width: 100%; font-size: {{Input::get('size', 70)}}px; font-family: 微軟正黑體;">
    <table class="_row table" style="text-align: right;">
        <thead>
            <tr class="yellow">
                <td>排名</td>
                <td>單位</td>
                <td>姓名</td>
                <td>日通數</td>
                <td>日分鐘</td>
                <td>月通數</td>
                <td>月時數</td>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr class="_row white _{{$row['排名']}} _show">
                <td class="red">{{ $row['排名'] }}</td>
                <td>{{ str_replace(['戶','經','營','部'], '', $row['部門']) }}</td>
                <td class="font-weight-bold">{{ $row['姓名'] }}</td>
                <td>{{ number_format($row['日通數']) }}</td>
                <td>{{ number_format($row['日分鐘']) }}</td>
                <td>{{ number_format($row['月通數']) }}</td>
                <td>{{ number_format($row['月時數']) }}</td>
            </tr>
            @endforeach            
        </tbody>
    </table>

     <table class="_group hide table" style="text-align: right;">
        <thead>
            <tr class="yellow">
                <td>單位</td>
                <td>日通數</td>
                <td>日分鐘</td>
                <td>月通數</td>
                <td>月時數</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($groups as $corpName => $group)
            <tr class="@if('總計' === $corpName){{'red'}}@else{{'white'}}@endif">
                <td class="font-weight-bold">{{ str_replace(['戶','經','營','部'], '', $corpName) }}</td>
                <td>{{ number_format($group['日通數']) }}</td>
                <td>{{ number_format($group['日分鐘']) }}</td>
                <td>{{ number_format($group['月通數']) }}</td>
                <td>{{ number_format($group['月時數']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="{!! URL::asset('/assets/js/jquery.min.js') !!}"></script>
<script src="{!! URL::asset('/assets/js/bootstrap.min.js') !!}"></script>
<script src="{!! URL::asset('/assets/js/helper.js') !!}"></script>
<script src="{!! URL::asset('/assets/js/boardmarqcti.js') !!}"></script>
<script>
var boardCti = new BoardMarqCti({"timeout": {{ Input::get('timeout', 10) }}, "size": parseInt({{Input::get('size', 0)}})});

boardCti.worker();
boardCti.resize();
</script>
</body>
</html>