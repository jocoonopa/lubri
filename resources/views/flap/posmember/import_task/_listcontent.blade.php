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
        <tr class="@if(\App\Model\Flap\PosMemberImportTask::BEEN_PUSHED_FLAG === ($content->status&\App\Model\Flap\PosMemberImportTask::BEEN_PUSHED_FLAG)){{'success'}}@endif" 
            data-toggle="popover" 
            data-placement="left" 
            data-trigger="hover"
            data-content="{{$content->memo}}"
            
            @if(\App\Model\Flap\PosMemberImportTask::BEEN_PUSHED_FLAG !== ($content->status&\App\Model\Flap\PosMemberImportTask::BEEN_PUSHED_FLAG))
            style="background-color: rgba(255, 5, 0, {{ $content->getOpacity() }});"
            @endif
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
                @if(\App\Model\Flap\PosMemberImportTask::BEEN_PUSHED_FLAG !== ($content->status&\App\Model\Flap\PosMemberImportTask::BEEN_PUSHED_FLAG))
                <a href="/flap/pos_member/import_push/{{ $task->id }}/content/{{ $content->id }}" class="pull-left btn btn-xs btn-raised btn-primary import-content-push" data-content-id="{{$content->id}}" data-content-name="{{$content->name}}">
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