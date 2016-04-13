@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h1>{{$title}} 
                <small>
                    <a href="/flap/pos_member/import_task?kind_id={{Input::get('kind_id')}}" class="btn btn-raised btn-sm btn-default">
                        <i class="glyphicon glyphicon-list"></i>回到列表
                    </a>
                </small>
            </h1><hr>

            @include('common.errormsg')
            
            {!! Form::model($task, [
                'url' => 'flap/pos_member/import_task?kind_id=' . Input::get('kind_id'), 
                'files' => true, 
                'id' => 'import-task']) 
            !!}
                @yield('form')
            {!! Form::close() !!}
        </div>
    </div> 
</div>
@stop

@section('js')
<script src="/assets/js/jquery.blockui.js"></script>
<script src="/assets/js/facade.js"></script>
<script src="/assets/js/importtask.js"></script>
@stop