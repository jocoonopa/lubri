@extends('base')

@section('title')
門市目標&PL設定
@stop

@section('css')
<link rel="stylesheet" href="{!! URL::asset('/assets/toastr/toastr.min.css') !!}">
@stop

@section('body')

<div class="row">
    <div class="col-md-12">
        <h1>門市目標&PL設定</h1><hr>
        
        <form action="#">
            <div class="form-group">
                {!! Form::label('year', '目標年份', ['class' => 'control-label']) !!}

                @include('pos.store_goal._yearSelect')
            </div>            
        </form>            
    </div>  
    
    @each('pos.store_goal._panel', $goalGroups, 'goals')
</div>

@stop

@section('js')
<script src="{!! URL::asset('/assets/toastr/toastr.min.js') !!}"></script>
<script src="{!! URL::asset('/assets/js/storegoal.js') !!}"></script>
<script>
var _token = '{{csrf_token()}}';
</script>  
@stop