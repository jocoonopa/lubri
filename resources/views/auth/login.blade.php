@extends('base')

@section('title')
使用者登入
@stop

@section('body')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-primary">
				<div class="panel-heading">從 {{ Request::getClientIp(true) }} 準備登入</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					@if(Session::has('warning'))
					    <div class="alert alert-warning" role="alert">
					        <span>{!! Session::get('warning') !!}</span>
					        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
					    </div>
					@endif

					{!! Form::open(['method' => 'POST', 'action' => 'Auth\AuthController@postLogin', 'class' => 'form-horizontal']) !!}
						@include ('auth.loginform', ['submitButtonText' => '登入'])
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection