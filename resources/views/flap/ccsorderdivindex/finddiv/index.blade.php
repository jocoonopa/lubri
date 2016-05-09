@extends('base')

@section('title')
尋找分寄單
@stop

@section('css')
<link rel="stylesheet" href="/assets/bootstrap-material-datetimepicker-gh-pages/css/bootstrap-material-datetimepicker.css">

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
@stop

@section('body')
    <div class="row">       
        <div class="col-md-12">     
            <h1>分寄單:出貨日</h1> 

            {!! Form::open(['method' => 'GET', 'action' => ['Flap\CCS_OrderDivIndex\FindDivController@index']]) !!}    
                <div class="form-group">
                    {!! Form::label('keyInDate', '出貨日', ['class' => 'col-md-1 control-label']) !!}
                    
                    <div class="col-md-4">{!! Form::text('start', $start,['id'=>'start', 'class' => 'form-control']) !!}</div>
                                        
                    <div class="col-md-4">{!! Form::text('end', $end,['id'=>'end', 'class' => 'form-control']) !!}</div>

                    <div class="col-md-2">{!! Form::button('<i class="glyphicon glyphicon-search"></i>', ['type' => 'submit', 'class' => 'btn btn-raised btn-primary btn-sm']) !!}</div>                    
                </div>
            {!! Form::close() !!}   

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>訂單單號</th>
                        <th>訂單日</th>
                        <th>出貨日</th>
                        <th>應付帳款</th>
                        <th>會員姓名</th>
                        <th>分寄單數</th>
                        <th>覆核日期</th>
                        <th>狀態</th>                        
                    </tr>                    
                </thead>
                <tbody>
                    @foreach ($orders as $key => $order)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>
                            <a href="http://192.168.100.68/chinghwa/iCCS/Order/OrderDetailsFrame.jsp?serNo={{$order['流水號']}}" target="_blank">{{$order['單號']}}</a>
                        </td>
                        <td>{{$order['訂單日']}}</td>
                        <td>{{$order['出貨日']}}</td>
                        <td>{{ number_format($order['應付帳款']) . '元'}}</td>
                        <td>{{$order['會員姓名']}}</td> 
                        <td>{{$order['分寄單數']}}</td>                       
                        <td>
                        @if (empty($order['覆核日']))
                            <span class="label label-default">尚未覆核</span>
                        @else
                            <span class="label label-info">{{$order['覆核日']}}</span>
                        @endif
                        </td>
                        <td>
                            @if (1 == $order['狀態'])
                                <span class="label label-success">{{'正常'}}</span>
                            @elseif(0 == $order['狀態'])
                                <span class="label label-default">{{'資料未完整'}}</span>
                            @elseif(-1 == $order['狀態'])
                                <span class="label label-warning">{{'取消訂單'}}</span>
                            @elseif(-2 == $order['狀態'])
                                <span class="label label-danger">{{'停止出貨'}}</span>
                            @else
                                <span class="label label-info">{{'未定義狀態'}}</span>
                            @endif
                        </td>                        
                    </tr>
                    @endforeach
                </tbody>
            </table>   
        </div>
    </div>
@stop

@section('js')
<script src="http://momentjs.com/downloads/moment-with-locales.min.js"></script>
<script src="/assets/bootstrap-material-datetimepicker-gh-pages/js/bootstrap-material-datetimepicker.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
moment.locale('zh-tw');
var bmdObg = {
    'time': false, 
    'clearButton': true,
    'cancelText': '取消',
    'okText': '確認',
    'clearText': '清除',
    'format': 'YYYYMMDD'
};

$('#start').add($('#end')).bootstrapMaterialDatePicker(bmdObg);   
</script>

@stop