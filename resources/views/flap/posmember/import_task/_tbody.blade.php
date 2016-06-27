<tr class=@if(NULL !== $task->executed_at)"success"@endif>
    <td><a href="/flap/pos_member/import_task/{{ $task->id }}">{{ $task->name }}</a></td>
    <td>{{ $task->import_cost_time . '秒'}}</td>
    <td>
        @if(NULL !== $task->executed_at){{ $task->execute_cost_time . '秒'}}
        @else <span class="label label-default">NOT YET</span>    
        @endif
    </td>
    <td>{{ $task->total_count . '筆' }}</td>
    {{-- <td>{{ $task->content()->isExecuted()->count() . '筆'}}</td> --}}
    <td>{{ $task->created_at->format('Y-m-d H:i:s') }}</td>
    {{--<td>@if(NULL !== $task->executed_at){{ $task->executed_at}}@else <span class="label label-default">NOT YET</span> @endif</td>--}}
    <td>{{ $task->user->username }}</td>
    <td>{!! $task->getStatusName() !!}</td>
    @if (\App\Model\Flap\PosMemberImportTask::STATUS_TOBEPUSHED === $task->status_code)
    <td>                            
        {!! Form::open(['method' => 'DELETE', 'action' => ['Flap\POS_Member\ImportTaskController@destroy', $task->id]]) !!}

        <a href="/flap/pos_member/import_push/{{ $task->id }}" class="btn btn-xs btn-raised btn-primary import-task-push" data-task-id="{{$task->id}}" data-task-name="{{$task->name}}">
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