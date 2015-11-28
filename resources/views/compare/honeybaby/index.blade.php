@extends('base')

@section('css')
<link rel="stylesheet" href="/assets/css/spinner.css" />
@stop

@section('body')
<div class="bs-docs-section clearfix">
	<div class="row">
		<div class="col-md-12">
		<h1>寵兒名單比對</h1><hr>
		
		@if ($res)
			<div class="well">{!! $res or NULL !!}</div>
		@endif

		{!! Form::open(array('route' => 'compare_honeybaby', 'files' => true)) !!}
		<div class="form-group">
			<a class="btn btn-sm btn-default" href="{{ route('compare_honeybaby_download_insert_example') }}"><i class="glyphicon glyphicon-floppy-save"></i>輔翼新增會員範例</a>
			<a class="btn btn-sm btn-default" href="{{ route('compare_honeybaby_download_update_example') }}"><i class="glyphicon glyphicon-floppy-save"></i>輔翼更新會員範例</a>
		</div>
		<div class="form-group">
			{!! Form::label('excel', '選擇比對檔案') !!}
			{!! Form::file(
				'excel', 
				['id'=>'excel','accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) 
			!!}
		</div>

		<div class="form-group">
			{!! Form::label('date', '名單年月') !!}
			{!! Form::selectRange('year', 2015, 2100, date('Y')) !!}
			{!! Form::selectMonth('month', date('m')) !!}
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
{{-- <div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div> --}}
@stop

@section('js')
<script src="/assets/js/jquery.blockui.js"></script>
<script src="/assets/js/facade.js"></script>
<script>
$('form').find('input[type="submit"]').click(function () {
	$blockUI(); 
});
</script>
@stop