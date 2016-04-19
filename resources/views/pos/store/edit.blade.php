@extends('base')

@section('body')
	<h1>修改門市</h1><hr>

	{!! Form::model($store, ['method' => 'PATCH', 'route' => ['pos.store.store.update', $store->id]]) !!}
		@include ('pos.store.store.form', ['submitButtonText' => '修改'])
	{!! Form::close() !!}

	@include ('errors.list')
@stop


 