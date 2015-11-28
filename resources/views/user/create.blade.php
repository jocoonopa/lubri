@extends('base')

@section('body')
	<h1>
		建立使用者
		<a class="btn btn-sm btn-default" href="{{ action('User\UserController@index') }}">
			<i class="glyphicon glyphicon-list"></i> 回到使用者列表
		</a>
	</h1><hr>

	{!! Form::open(['action' => 'User\UserController@store']) !!}
		@include ('user.form', ['submitButtonText' => '建立使用者'])
	{!! Form::close() !!}

	@include ('errors.list')
@stop