@extends('base')

@section('css')
<link rel="stylesheet" type="text/css" href="/assets/css/snackbar.css">
@stop

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ $task->name}} 
                <small>
                    <a href="/flap/pos_member/import_task?kind_id={{ $task->kind()->first()->id }} " class="btn btn-raised btn-sm btn-default">
                        <i class="glyphicon glyphicon-list"></i>
                        任務列表
                    </a>
                
                    @if (NULL === $task->executed_at)
                    <a href="/flap/pos_member/import_task/{{$task->id}}/edit" class="btn btn-sm btn-default citem">
                        <i class="glyphicon glyphicon-pencil"></i>
                        編輯任務{{ '&nbsp;' . $task->name}}
                    </a>

                    <a href="/flap/pos_member/import_task/{{$task->id}}/content/create" class="btn btn-sm btn-default citem">
                        <i class="glyphicon glyphicon-plus"></i>
                        {{ '新增&nbsp;' . $task->name . '&nbsp;項目'}}
                    </a>         
                    
                    <a href="/flap/pos_member/import_push/{{ $task->id }}" class="pull-right btn btn-sm btn-raised btn-primary import-task-push citem" data-task-id="{{$task->id}}" data-task-name="{{$task->name}}">
                        <i class="glyphicon glyphicon-play"></i>
                        推送</a>
                    @endif
                </small>
                <p>
                    <small class="ratio hide">
                        <b>{!! $task->getStatusName() !!}</b>
                        <span class="badge current"></span>/<span class="badge total"></span>
                    </small>
                    
                    <div class="progress hide">                        
                        <div class="progress-bar progress-bar-warning" style="width: 0%"></div>
                    </div>
                </p>
            </h1><hr>

            @include('common.successmsg')
            @include('common.errormsg')                                    
            @include('flap.posmember.import_task._detail')
            @include('flap.posmember.import_task._searchnull') 
            
            {!! $contents->appends(\Input::all())->render() !!}           
            @include('flap.posmember.import_task._listcontent')
        </div>
    </div>  
</div>
<span class="hide my-snackbar" data-toggle=snackbar data-content="資料載入完成!">&nbsp;</span>
@stop

@section('js')
<script src="/assets/js/jquery.blockui.js"></script>
<script src="/assets/js/facade.js"></script>
<script src="/assets/js/importtask.js"></script>
<script src="/assets/js/snackbar.js"></script>
<script>
@if (!empty(\Input::all()))
var options =  {
    style: "toast", // add a custom class to your snackbar
    timeout: 100 // time in milliseconds after the snackbar autohides, 0 is disabled
};

$.snackbar(options);

setTimeout(function () {
    $('.my-snackbar').snackbar('show');
}, 1000);
@endif


@if (in_array($task->status_code, [\App\Model\Flap\PosMemberImportTask::STATUS_IMPORTING, \App\Model\Flap\PosMemberImportTask::STATUS_PUSHING]))

function loadProgress(id)
{
    $('.citem').addClass('hide');
    $('.progress').add('.ratio').removeClass('hide');

    var current = 0;
    var currentStep = 0;

    $.getJSON('/flap/pos_member/import_task/' + id +'/progress', function (res) {
        if ({{\App\Model\Flap\PosMemberImportTask::STATUS_IMPORTING}} === res.status_code) {
            current = res.imported_count;

            currentStep = Math.floor((res.imported_count/res.total) * 100);
        } 

        if ({{\App\Model\Flap\PosMemberImportTask::STATUS_PUSHING}} === res.status_code) {
            current = res.pushed_count;

            currentStep = Math.floor((res.pushed_count/res.total) * 100);
        }

        if (true === res.is_acting) {
            $('.ratio').find('.current').text(current);
            $('.ratio').find('.total').text(res.total);
            $('.progress-bar').css('width', currentStep + '%');

            setTimeout(function () {loadProgress(id)}, 1500);
        } else {
            $('.ratio').find('.current').text(res.total);
            $('.ratio').find('.total').text(res.total);
            $('.progress-bar').css('width', '100%');
            
            setTimeout(function () {location.reload();}, 1500);
        }
    });
}

loadProgress({{$task->id}});

@endif

</script>
@stop
