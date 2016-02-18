@extends('base')

@section('css')
<link rel="stylesheet" href="/assets/bootstrap-material-datetimepicker-gh-pages/css/bootstrap-material-datetimepicker.css">
@stop

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
           <h1>{{ $content->name . '資料修改'}} 
            <div class="ripple-container"></div></a>
            <small><a href="/flap/pos_member/import_task/{{$task->id}}/content" class="btn btn-raised btn-sm btn-default"><i class="glyphicon glyphicon-list"></i>回到列表</a></small></h1>
                
            @include('common.errormsg')

           {!! Form::model($content, [
                'method' => 'PUT',
                'url' => 'flap/pos_member/import_task/' . $task->id . '/content/' . $content->id
            ]) !!}
                <div class="form-group">
                    {!! Form::label(
                        'name', 
                        '*會員姓名', 
                        ['class' => 'control-label']) 
                    !!}

                    {!! Form::text(
                        'name', 
                        NULL,
                        ['id'=>'name', 'class' => 'form-control', 'required' => true, 'placeholder' => '請輸入會員姓名']) 
                    !!}

                    <p class="help-block">{{'Example: 王大明'}}</p>
                </div>
                <div class="form-group">
                    {!! Form::label(
                        'email', 
                        '電子信箱', 
                        ['class' => 'control-label']) 
                    !!}

                    {!! Form::email(
                        'email', 
                        NULL,
                        ['id'=>'email', 'class' => 'form-control', 'placeholder' => '請輸入會員信箱']) 
                    !!}

                    <p class="help-block">{{'Example: example@domain.com'}}</p>
                </div>
                <div class="form-group">
                    {!! Form::label(
                        'cellphone', 
                        '手機', 
                        ['class' => 'control-label']) 
                    !!}

                    {!! Form::text(
                        'cellphone', 
                        NULL,
                        ['id'=>'cellphone', 'class' => 'form-control', 'placeholder' => '請輸入會員手機']) 
                    !!}

                    <p class="help-block">{{'Example: 0987654321'}}</p>
                </div>
                <div class="form-group">
                    {!! Form::label(
                        'hometel', 
                        '住家電話', 
                        ['class' => 'control-label']) 
                    !!}

                    {!! Form::text(
                        'hometel', 
                        NULL,
                        ['id'=>'hometel', 'class' => 'form-control', 'placeholder' => '請輸入會員住家電話']) 
                    !!}

                    <p class="help-block">{{'Example: 0223897654'}}</p>
                </div>
                <div class="form-group">
                    {!! Form::label(
                        'officetel', 
                        '公司電話', 
                        ['class' => 'control-label']) 
                    !!}

                    {!! Form::text(
                        'officetel', 
                        NULL,
                        ['id'=>'officetel', 'class' => 'form-control', 'placeholder' => '請輸入會員公司電話']) 
                    !!}

                    <p class="help-block">{{'Example: 0223897654'}}</p>
                </div>

                <div class="form-group">
                    {!! Form::label(
                        'zipcode', 
                        '郵遞區號/縣市/區', 
                        ['class' => 'control-label']) 
                    !!}
                    
                    <div id="twzipcode">
                        <div data-role="county" data-style="form-control" data-value="{{$content->getCityName()}}"></div>
                        <div data-role="district" data-style="form-control" data-value="{{$content->getStateName()}}"></div>
                        <div data-role="zipcode" data-style="form-control" data-value="{{$content->getZipcode()}}"></div>
                    </div>                             

                    <p class="help-block">{{'Example: 235'}}</p>
                </div>

                <div class="form-group">
                    {!! Form::label(
                        'homeaddress', 
                        '住家地址', 
                        ['class' => 'control-label']) 
                    !!}
    
                    {!! Form::text('homeaddress', NULL, ['id'=>'homeaddress', 'class' => 'form-control', 'placeholder' => '請輸入會員住家地址']);
                    !!}

                    <p class="help-block">{{'Example: 愛國東路一段32號'}}</p>
                </div>

                <div class="form-group">
                    {!! Form::label(
                        'period_at', 
                        '預產期', 
                        ['class' => 'control-label']) 
                    !!}

                    {!! Form::text(
                        'period_at', 
                        NULL,
                        ['id'=>'period_at', 'class' => 'form-control', 'placeholder' => '請選擇會員預產期']) 
                    !!}
                </div>

                <div class="form-group">
                    {!! Form::label(
                        'hospital', 
                        '生產醫院', 
                        ['class' => 'control-label']) 
                    !!}

                    {!! Form::text(
                        'hospital', 
                        NULL,
                        ['id'=>'hospital', 'class' => 'form-control', 'placeholder' => '請輸入會員生產醫院']) 
                    !!}

                    <p class="help-block">{{'Example: 臺大醫院'}}</p>
                </div>
                <div class="form-group">
                    {!! Form::submit('確認', ['class' => 'btn btn-raised btn-primary btn-sm']) !!}

                    <a class="btn btn-raised btn-default btn-sm" href="/flap/pos_member/import_task/{{$task->id}}/content">取消</a>
                </div>
           {!! Form::close() !!}
        </div>
    </div>  
</div>
@stop

@section('js')
<script src="http://momentjs.com/downloads/moment-with-locales.min.js"></script>
<script src="/assets/bootstrap-material-datetimepicker-gh-pages/js/bootstrap-material-datetimepicker.js"></script>
<script src="/assets/jQuery-TWzipcode-master/jquery.twzipcode.min.js"></script>
<script>
moment.locale('zh-tw');
$('#period_at').bootstrapMaterialDatePicker({'time': false});
$('#twzipcode').twzipcode();
</script>
@stop
