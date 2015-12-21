@extends('base')

@section('title') 
景華商品複製為康萃特商品
@stop

@section('body')
    <div class="row">
        <div class="col-md-12">
            <h2>將指定景華商品複製為康萃特商品</h2><hr>
        </div>
        
        <div class="col-md-12">
            <h4>請輸入商品產編搜尋</h4>
            
            @include ('common.successmsg')
            @include ('common.errormsg')

            {!! Form::open(['method' => 'GET', 'action' => ['Flap\PIS_Goods\CopyToCometrustController@index'], 'id' => 'search']) !!}    
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" name="code" class="form-control" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </div>
                </div>
            {!! Form::close() !!}
            
            @if (0 < count($goodses))
            {!! Form::open(['method' => 'POST', 'action' => ['Flap\PIS_Goods\CopyToCometrustController@store'], 'id' => 'insert']) !!}    
                @include('flap.pisgoods.copytocometrust.form', ['goodses' => $goodses])
            {!! Form::close() !!}
            @endif
        </div>
    </div>
@stop

@section('js')

@stop