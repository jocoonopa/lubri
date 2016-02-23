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
            
            <li class="list-group-item">{{ '會員類別:&nbsp;&nbsp;'}} <b>{{ $task->category }}</b></li>
            <li class="list-group-item">{{ '會員區別:&nbsp;&nbsp;'}} <b>{{ $task->distinction }}</b></li>
            <li class="list-group-item">{{ '新客旗標:&nbsp;&nbsp;'}} <b>{{ $task->getInsertFlagString() }}</b></li>
            <li class="list-group-item">{{ '舊客旗標:&nbsp;&nbsp;'}} <b>{{ $task->getUpdateFlagString() }}</b></li>
            <li class="list-group-item">{{ '建立人員:&nbsp;&nbsp;'}} <b>{{ $task->user->username }}</b></li>                        
        </ul>

        <a class="label label-primary" href="/flap/pos_member/import_task/{{$task->id}}?is_exist=no">{{ '新會員 ' . $task->content()->where('is_exist', '=', false)->count() . ' 位'}}</a>
        <a class="label label-default" href="/flap/pos_member/import_task/{{$task->id}}?is_exist=yes">{{ '舊會員 ' . $task->content()->where('is_exist', '=', true)->count() . ' 位'}}</a>

        <a class="label label-success" href="/flap/pos_member/import_task/{{$task->id}}">{{ '總計 ' . $task->content()->count() . ' 位'}}</a>

        <button data-href="/flap/pos_member/import_task/{{$task->id}}/export" data-task-id="{{$task->id}}" class="pull-right btn btn-raised btn-sm btn-primary import-task-export">
           <i class="glyphicon glyphicon-download-alt"></i> 匯出
        </button>
    </div>
</div>