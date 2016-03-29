@extends('base')

@section('title') 
郵遞區號轉換
@stop

@section('body')
    <div class="row">       
        <div class="col-md-12">     
            @include('common.errormsg')

            <h1>郵遞區號轉換</h1> 
            
            {!! Form::open(['method' => 'post', 'files' => true, 'action' => ['Test\TranZipcodeController@index']]) !!}    
                @include('test.tran_zipcode._form')
            {!! Form::close() !!}   
        </div>
    </div>
@stop
