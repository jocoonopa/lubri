@extends('base')

@section('title')
忘記密碼
@stop

@section('body')
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-primary">
				<div class="panel-heading">忘記密碼</div>
				<div class="panel-body">
					{!! Form::open(['method' => 'POST', 'action' => 'Auth\PasswordController@postEmail', 'class' => 'form-horizontal']) !!}
						@include('common.successmsg')
						
						@unless(Session::has('success')) 
							@include('auth.forgetpasswordform', ['submitButtonText' => '確認'])
						@endunless 
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
@stop
