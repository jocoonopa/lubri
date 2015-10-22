@extends('base')

@section('body')
	<h1>Write article</h1><hr>

	{!! Form::open(['action' => 'ArticlesController@store']) !!}
		@include ('articles.form', ['submitButtonText' => 'Add Article'])
	{!! Form::close() !!}

	@include ('errors.list')
@stop