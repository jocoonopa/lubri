@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h1>{{$title}} <small><a href="/flap/pos_member/import_activity_task/create" class="btn btn-raised btn-sm btn-primary">
            <i class="glyphicon glyphicon-plus"></i>
            新增任務</a></small></h1>

            @include('common.successmsg')
            @include('common.errormsg')

            <table class="table">
                <thead>
                    <tr>
                        <th>任務</th>
                        <th>匯入費時</th>
                        <th>推送費時</th>
                        <th>待推送</th>
                        <th>已推送</th>
                        <th>建立時間</th>
                        <th>推送完成時間</th>
                        <th>建立者</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr class=@if(NULL !== $task->executed_at)"success"@endif>
                        <td><a href="/flap/pos_member/import_act_task/{{ $task->id }}">{{ $task->name }}</a></td>
                        <td>{{ $task->import_cost_time . '秒'}}</td>
                        <td>@if(NULL !== $task->executed_at){{ $task->execute_cost_time . '秒'}}@else <span class="label label-default">NOT YET</span>    @endif</td>
                        <td>{{ ($task->insert_count + $task->update_count) . '筆' }}</td>
                        <td>{{ $task->content()->isExecuted()->count() . '筆'}}</td>
                        <td>{{ $task->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>@if(NULL !== $task->executed_at){{ $task->executed_at}}@else <span class="label label-default">NOT YET</span> @endif</td>
                        <td>{{ $task->user->username }}</td>
                        @if (NULL === $task->executed_at)
                        <td>                            
                            {!! Form::open(['method' => 'DELETE', 'action' => ['Flap\POS_Member\ImportActivityTaskController@destroy', $task->id]]) !!}

                            <a href="/flap/pos_member/import_act_push/{{ $task->id }}" class="btn btn-xs btn-raised btn-primary import-task-push" data-task-id="{{$task->id}}" data-task-name="{{$task->name}}">
                                <i class="glyphicon glyphicon-play"></i>
                                
                            </a>
                                <button type="submit" class="btn btn-raised btn-xs btn-danger import-task-delete" data-task-id="{{$task->id}}" data-task-name="{{$task->name}}">
                                    <i class="glyphicon glyphicon-remove"></i>
                                    
                                </button>
                            {!! Form::close() !!}
                        </td>
                        @else
                        <td></td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>                    
            </table>
        </div>
    </div>  
</div>
@stop

@section('js')
<script src="/assets/js/jquery.blockui.js"></script>
<script src="/assets/js/facade.js"></script>
@stop

