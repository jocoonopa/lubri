@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ '任務' . $task->id . '號'}} 
            <small><a href="/flap/pos_member/import_task" class="btn btn-raised btn-sm btn-default">
                <i class="glyphicon glyphicon-list"></i>
                回到任務列表
            </a><a href="/flap/pos_member/import_task/{{$task->id}}/content/create" class="btn btn-raised btn-sm btn-primary">
                <i class="glyphicon glyphicon-plus"></i>
                新增項目
            </a>
            
            <a href="/flap/pos_member/import_push/pull/{{$task->id}}" class="pull-right btn btn-raised btn-sm btn-info">
                <i class="glyphicon glyphicon-refresh"></i>
                同步
            </a>
            </small>

            </h1><hr>

            @include('common.successmsg')
            
            <div class="panel panel-default">
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">匯入耗時: <b>{{ $task->import_cost_time . '秒' }}</b></li>
                        <li class="list-group-item">匯入成功筆數: <b>{{ ($task->insert_count + $task->update_count) . '筆' }}</b></li>
                        <li class="list-group-item">匯入失敗筆數: <b>{{ $task->error_count . '筆'}}</b></li>
                           <li class="list-group-item">建立時間:  <b>{{$task->created_at->format('Y-m-d H:i:s')}}</b></li>
                        <li class="list-group-item">更新時間:  <b>{{$task->updated_at->format('Y-m-d H:i:s')}}</b></li>
                        <li class="list-group-item">推送完成時間: @if($task->executed_at) <span class="label label-success">{{$task->executed_at }}</span> @else <span class="label label-default">NOT YET</span> @endif</li>
                        <li class="list-group-item">建立人員: <b>{{ $task->user->username }}</b></li>
                    </ul>
                </div>
            </div>
            
            <table class="table table-striped">
                <thead>
                    <th>姓名</th>
                    <th>Email</th>
                    <th>手機</th>
                    <th>家裡電話</th>
                    <th>區碼</th>
                    <th>縣市</th>
                    <th>區</th>
                    <th>住址</th>
                    <th>最後更新時間</th>
                    <th>操作</th>
                </thead>
                <tbody>
                    @foreach ($contents as $content)
                    <tr class="@if(32 === ($content->status&32)){{'success'}}@endif" 
                        data-toggle="popover" 
                        data-placement="left" 
                        data-trigger="hover"
                        data-content="
                        @if (true === $content->is_exist)<b> {{$content->code}} </b><br>@endif
                        <b>狀態:</b>  {{ str_pad(decbin($content->status), 6, 0, STR_PAD_LEFT) }}<br>
                        <b>生產醫院:</b>  {{$content->hospital}}<br>
                        <b>預產期:</b>  {{$content->period_at}}"
                    >
                        <td>
                            <a href="/flap/pos_member/import_task/{{$content->pos_member_import_task_id}}/content/{{$content->id}}">{{ $content->name }}                                
                            </a> 
                            @if (true === $content->is_exist) <span class="label label-warning">舊會員</span>@endif                            
                        </td>
                        <td>{{ $content->email }}</td>
                        <td>{{ $content->cellphone }}</td>
                        <td>{{ $content->hometel }}</td>
                        <td>{{ $content->zipcode }}</td>
                        <td>{{ $content->city }}</td>
                        <td>{{ $content->state }}</td>
                        <td>{{ $content->homeaddress }}</td>
                        <td>{{ $content->updated_at->format('Y-m-d H:i')}}</td>
                        <td>    
                            @if(32 !== ($content->status&32))
                            <a href="/flap/pos_member/import_push/{{ $task->id }}/content/{{ $content->id }}" class="pull-left btn btn-xs btn-raised btn-primary import-content-push" data-content-name="{{$content->name}}">
                                    <i class="glyphicon glyphicon-play"></i>                                
                            </a>   
                            @endif    
                            <a href="/flap/pos_member/import_task/{{ $task->id }}/content/{{ $content->id }}/edit" class="pull-left btn btn-xs btn-raised btn-default" data-task-id="{{$task->id}}">
                                <i class="glyphicon glyphicon-pencil"></i>
                                
                            </a>                                            
                            
                            {!! Form::open(['method' => 'DELETE', 'url' => "/flap/pos_member/import_task/{$task->id}/content/{$content->id}", 'class' => 'pull-left']) !!}
                                <button type="submit" class="btn btn-raised btn-xs btn-danger import-content-delete" data-content-name="{{$content->name}}">
                                    <i class="glyphicon glyphicon-remove"></i>
                                    
                                </button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {!! $contents->render() !!}
    </div>  
</div>
@stop

@section('js')
<script>
$('.import-content-delete').click(function () {
    var $this = $(this);

    bootbox.confirm({
        size: 'small',
        message: '確定將 <b>' + $this.data('content-name') + '</b> 從任務移除嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            return (true === result) ? $this.closest('form').submit() : this.modal('hide');
        }}); 

    return false;
});

$('.import-content-push').click(function () {
    var $this = $(this);

    bootbox.confirm({
        size: 'small',
        message: '確定推送項目' + $this.data('content-name') + '嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            return (true === result) ? window.location.href=$this.attr('href') : this.modal('hide');
        }}); 

    return false;
});

$('tr').popover({
    "html": true,
    "triger": "hover"
});
</script>
@stop
