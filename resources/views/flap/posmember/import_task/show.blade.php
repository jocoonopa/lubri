@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ '任務' . $task->id . '號'}} 
            <small><a href="/flap/pos_member/import_task" class="btn btn-raised btn-sm btn-default">
                <i class="glyphicon glyphicon-list"></i>
                回到任務列表
            </a>
            
            @if (NULL === $task->executed_at)
            <a href="/flap/pos_member/import_task/{{$task->id}}/content/create" class="btn btn-raised btn-sm btn-primary">
                <i class="glyphicon glyphicon-plus"></i>
                新增項目
            </a>

            <a href="/flap/pos_member/import_push/pull/{{$task->id}}" class="pull-right btn btn-raised btn-sm btn-info import-task-pull" data-task-id="{{$task->id}}">
                <i class="glyphicon glyphicon-refresh"></i>
                同步
            </a>            
            
            <a href="/flap/pos_member/import_push/{{ $task->id }}" class="pull-right btn btn-sm btn-raised btn-default import-task-push" data-task-id="{{$task->id}}">
                <i class="glyphicon glyphicon-play"></i>
                推送任務</a>
            @endif
            </small>

            </h1><hr>

            @include('common.successmsg')
            @include('common.errormsg')
            
            <div class="panel panel-default">
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">{{'匯入耗時:&nbsp;'}}<b>{{ $task->import_cost_time . '秒' }}</b></li>
                        @if($task->executed_at)<li class="list-group-item">{{'推送耗時:&nbsp;'}}<b>{{ $task->execute_cost_time . '秒' }}</b></li> @endif
                        <li class="list-group-item">{{ '匯入成功筆數:&nbsp;'}} <b>{{ ($task->insert_count + $task->update_count) . '筆' }}</b></li>
                        <li class="list-group-item">{{ '匯入失敗筆數:&nbsp;'}} <b>{{ $task->error_count . '筆'}}</b></li>
                           <li class="list-group-item">{{ '建立時間:&nbsp;&nbsp;'}}  <b>{{$task->created_at->format('Y-m-d H:i:s')}}</b></li>
                        <li class="list-group-item">{{ '更新時間:&nbsp;&nbsp;'}}  <b>{{$task->updated_at->format('Y-m-d H:i:s')}}</b></li>
                        <li class="list-group-item">{{ '推送完成時間:&nbsp;&nbsp;'}} @if($task->executed_at) <span class="label label-success">{{$task->executed_at }}</span> @else <span class="label label-default">NOT YET</span> @endif</li>

                        <li class="list-group-item">{{ '新客旗標:&nbsp;&nbsp;'}} <b>{{ $task->insert_flags }}</b></li>
                        <li class="list-group-item">{{ '舊客旗標:&nbsp;&nbsp;'}} <b>{{ $task->update_flags }}</b></li>
                        <li class="list-group-item">{{ '建立人員:&nbsp;&nbsp;'}} <b>{{ $task->user->username }}</b></li>                        
                    </ul>

                    <span class="label label-primary">{{ '新會員 ' . $task->content()->where('is_exist', '=', false)->count() . ' 位'}}</span>
                    <span class="label label-default">{{ '舊會員 ' . $task->content()->where('is_exist', '=', true)->count() . ' 位'}}</span>
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
                        data-content="{{$content->memo}}"
                    >
                        <td>
                            {{ $content->name }}                              
                            @if (true === $content->is_exist)<br><p class="label label-warning">舊會員</p>@endif
                            @if (NULL !== $content->code)<a class="label label-default" href="jocoonopaieopen://192.168.100.68/chinghwa/iDCS/Member/viewmember.fl?memberSerNo:{{$content->sernoi}}">{{$content->code}}</a>@endif 
                        </td>
                        <td>{{ $content->email }}</td>
                        <td>{{ $content->cellphone }}</td>
                        <td>
                            @if(isWrongCodeTel($content)) 
                                {{ $content->hometel }}
                            @else 
                                <span class="label label-warning">{{ $content->hometel }}</span> 
                            @endif                            
                        </td>
                        <td>{{ $content->getZipcode() }}</td>
                        <td>{{ $content->getCityName() }}</td>
                        <td>{{ $content->getStateName() }}</td>
                        <td>{{ $content->homeaddress }}</td>
                        <td>{{ $content->updated_at->format('Y-m-d H:i')}}</td>
                        <td>    
                            @if(32 !== ($content->status&32))
                            <a href="/flap/pos_member/import_push/{{ $task->id }}/content/{{ $content->id }}" class="pull-left btn btn-xs btn-raised btn-primary import-content-push" data-content-name="{{$content->name}}">
                                    <i class="glyphicon glyphicon-play"></i>                                
                            </a>   

                            <a href="/flap/pos_member/import_task/{{ $task->id }}/content/{{ $content->id }}/edit" class="pull-left btn btn-xs btn-raised btn-default" target="_blank" data-task-id="{{$task->id}}">
                                <i class="glyphicon glyphicon-pencil"></i>
                                
                            </a>                                            
                            
                            {!! Form::open(['method' => 'DELETE', 'url' => "/flap/pos_member/import_task/{$task->id}/content/{$content->id}", 'class' => 'pull-left']) !!}
                                <button type="submit" class="btn btn-raised btn-xs btn-danger import-content-delete" data-content-name="{{$content->name}}">
                                    <i class="glyphicon glyphicon-remove"></i>
                                    
                                </button>
                            {!! Form::close() !!}
                            @endif
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
<script src="/assets/js/jquery.blockui.js"></script>
<script src="/assets/js/facade.js"></script>
<script src="/assets/js/importtask.js"></script>
@stop
