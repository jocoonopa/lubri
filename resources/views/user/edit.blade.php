@extends('base')

@section('title')
修改使用者
@stop

@section('body')
	<h1>
		{{ $user->username }}
		<small>
			<a class="btn btn-sm btn-default" href="{{ action('User\UserController@index') }}">
				<i class="glyphicon glyphicon-list"></i> 回到使用者列表
			</a>
		</small>
	</h1><hr>
	
	{!! Form::model($user, ['method' => 'PATCH', 'action' => ['User\UserController@update', $user->id]]) !!}
		@include('common.successmsg')
		
		<div class="form-group">
			<label for="code">輔翼編號:</label>
			<input id="code" class="form-control" name="code" type="text" value="{{$user->code}}" readonly>
		</div>

		<div class="form-group">
			<label for="serno">輔翼SerNo:</label>
			<input id="serno" class="form-control" name="serno" type="text" value="{{$user->serno}}" readonly>
		</div>

		@include ('user.profileform')

		<div class="form-group">
			{!! Form::submit('確認', ['class' => 'btn btn-primary form-control']) !!}
		</div>
	{!! Form::close() !!}
@stop