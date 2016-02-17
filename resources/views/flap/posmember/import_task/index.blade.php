@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
	<div class="row">
		<div class="col-md-12">
			<h1>會員匯入任務列表 <small><a href="/flap/pos_member/import_task/create" class="btn btn-raised btn-sm btn-primary">
			<i class="glyphicon glyphicon-plus"></i>
			新增任務</a></small></h1>

			@include('common.successmsg')
			@include('common.errormsg')

			<table class="table">
				<thead>
					<tr>
						<th>編號</th>
						<th>匯入費時</th>
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
						<td><a href="/flap/pos_member/import_task/{{ $task->id }}">{{ $task->id }}</a></td>
						<td>{{ $task->import_cost_time . '秒'}}</td>
						<td>{{ ($task->insert_count + $task->update_count) }}</td>
						<td>{{ $task->content()->isExecuted()->count() }}</td>
						<td>{{ $task->created_at->format('Y-m-d H:i:s') }}</td>
						<td>{{ $task->executed_at}}</td>
						<td>{{ $task->user->username }}</td>
						@if (NULL === $task->executed_at)
						<td>							
							{!! Form::open(['method' => 'DELETE', 'action' => ['Flap\POS_Member\ImportTaskController@destroy', $task->id]]) !!}

							<a href="/flap/pos_member/import_push/{{ $task->id }}" class="btn btn-xs btn-raised btn-primary import-task-push" data-task-id="{{$task->id}}">
								<i class="glyphicon glyphicon-play"></i>
								
							</a>
								<button type="submit" class="btn btn-raised btn-xs btn-danger import-task-delete" data-task-id="{{$task->id}}">
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
<script>
$('.import-task-delete').click(function () {
	var $this = $(this);

	bootbox.confirm({
		size: 'small',
		message: '確定刪除任務' + $this.data('task-id') + '嗎?', 
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

$('.import-task-push').click(function () {
	var $this = $(this);

	bootbox.confirm({
		size: 'small',
		message: '確定執行任務' + $this.data('task-id') + '嗎?', 
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
</script>
@stop

