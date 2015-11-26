@extends('base')

@section('body')
	<h1>建立使用者</h1><hr>

	{!! Form::open(['action' => 'User\UserController@store']) !!}
		@include ('user.form', ['submitButtonText' => '建立使用者'])
	{!! Form::close() !!}

	@include ('errors.list')
@stop