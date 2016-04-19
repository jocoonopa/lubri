@extends('base')

@section('body')
	<h1>新增門市</h1><hr>

	{!! Form::open(['action' => 'Pos\Store\StoreController@store']) !!}
		@include ('pos.store.store.form', ['submitButtonText' => '新增'])
	{!! Form::close() !!}

	@include ('errors.list')
@stop