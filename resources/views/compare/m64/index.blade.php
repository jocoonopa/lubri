@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
	<div class="row">
		<div class="col-md-12">
		<h1>64期閱刊比對</h1><hr>
		
		@if ($res)
			<div class="well">{{ $res or NULL }}</div>
		@endif

		{!! Form::open(array('url' => 'compare/m64', 'files' => true)) !!}
		<div class="form-group">
			{!! Form::label('excel', '選擇比對檔案') !!}
			{!! Form::file(
				'excel', 
				['id'=>'excel','accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) 
			!!}
		</div>

		<div class="form-group">
			{!! Form::submit('比對', ['class' => 'btn btn-primary btn-sm']) !!}
		</div>
		{!! Form::close() !!}
		</div>
	</div>	

	@if (count($errors) > 0)
	    <div class="alert alert-danger">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif
</div>
@stop