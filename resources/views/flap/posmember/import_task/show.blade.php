@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ $task->name}} 
            <small><a href="/flap/pos_member/import_task" class="btn btn-raised btn-sm btn-default">
                <i class="glyphicon glyphicon-list"></i>
                任務列表
            </a>
            
            @if (NULL === $task->executed_at)
            <a href="/flap/pos_member/import_task/{{$task->id}}/edit" class="btn btn-sm btn-default">
                <i class="glyphicon glyphicon-pencil"></i>
                編輯任務{{ '&nbsp;' . $task->name}}
            </a>

            <a href="/flap/pos_member/import_task/{{$task->id}}/content/create" class="btn btn-sm btn-default">
                <i class="glyphicon glyphicon-plus"></i>
                {{ '新增&nbsp;' . $task->name . '&nbsp;項目'}}
            </a>

            <a href="/flap/pos_member/import_push/pull/{{$task->id}}" class="pull-right btn btn-sm btn-info import-task-pull" data-task-id="{{$task->id}}" data-task-name="{{$task->name}}">
                <i class="glyphicon glyphicon-refresh"></i>
                同步
            </a>            
            
            <a href="/flap/pos_member/import_push/{{ $task->id }}" class="pull-right btn btn-sm btn-raised btn-primary import-task-push" data-task-id="{{$task->id}}" data-task-name="{{$task->name}}">
                <i class="glyphicon glyphicon-play"></i>
                推送</a>
            @endif
            </small>
            </h1><hr>

            @include('common.successmsg')
            @include('common.errormsg')                                    
            @include('flap.posmember.import_task._detail')
            @include('flap.posmember.import_task._searchnull')            
            @include('flap.posmember.import_task._listcontent')
        </div>

        {!! $contents->appends(\Input::all())->render() !!}
    </div>  
</div>
@stop

@section('js')
<script src="/assets/js/jquery.blockui.js"></script>
<script src="/assets/js/facade.js"></script>
<script src="/assets/js/importtask.js"></script>
@stop
