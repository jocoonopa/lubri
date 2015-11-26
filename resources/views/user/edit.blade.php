@extends('base')

@section('title')
修改使用者
@stop

@section('body')
	<h1>{{ $user->username }}</h1><hr>

	{!! Form::model($user, ['method' => 'PATCH', 'action' => ['User\UserController@update', $user->id]]) !!}
		@include ('user.profileform')

		<div class="form-group">
			{!! Form::submit('確認', ['class' => 'btn btn-primary form-control']) !!}
		</div>
	{!! Form::close() !!}

	<a href="{{ action('User\UserController@index') }}">回到使用者列表</a>
@stop