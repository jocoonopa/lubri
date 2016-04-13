@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h1>{{'任務' . $task->name . '編輯'}} <small><a href="/flap/pos_member/import_task" class="btn btn-raised btn-sm btn-default">
                <i class="glyphicon glyphicon-list"></i>
                任務列表
            </a>

            <a href="/flap/pos_member/import_task/{{$task->id}}" class="btn btn-sm btn-default">
                <i class="glyphicon glyphicon-circle-arrow-left"></i> {{ '回到詳情' }}
            </a>
            </small></h1><hr>

            @include('common.errormsg')

            {!! Form::model($task, [
                'url' => 'flap/pos_member/import_task/' . $task->id, 
                'id' => 'import-task', 
                'method' => 'PUT']) 
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
@stop