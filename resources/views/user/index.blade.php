@extends('base')

@section('title')
使用者列表
@stop

@section('body')

<h1>
	使用者列表
	<small>
		<a class="btn btn-sm btn-primary" href="{{ action('User\UserController@create') }}">
			<i class="glyphicon glyphicon-plus"></i> 新增
		</a>
	</small>
</h1><hr>

@include('common.successmsg')

<table class="table table-striped">
	<thead>
		<tr>
			<td>部門</td>
			<td>帳號</td>
			<td>姓名</td>
			<td>分機</td>
			<td>ip</td>
			<td>動作</td>
		</tr>
	</thead>
	<tbody>
		@foreach ($users as $user)
		<tr>
			<td>{{ $user->corp }}</td>
			<td><a href="mailto: {{ $user->account . '@' . env('DOMAIN')}}">{{ $user->account }}</a></td>
			<td>{{ $user->username }}</td>
			<td>{{ $user->ext }}</td>
			<td>{{ $user->ip }}</td>
			<td>
				{!! Form::open(['method' => 'DELETE', 'action' => ['User\UserController@destroy', $user->id]]) !!}
					<a class="btn btn-xs btn-warning" href="{{ action('User\UserController@edit', ['id' => $user->id]) }}">
						<i class="glyphicon glyphicon-pencil"></i>
					</a>

					<button type="submit" class="btn btn-xs btn-danger delete" data-name="{{$user->username}}">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
				{!! Form::close() !!}
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
@stop

@section('js')
<script>
(function () {
	$('button.delete').click(function () {
		var $this = $(this);
		var $form = $this.closest('form');
		var name = $this.data('name');

		bootbox.confirm('確定刪除<b>' + name +'</b>嗎?', function(result) {
			if (result) {
				$form.submit();
			}
		}); 

		return false;
	});
})();
</script>
@stop