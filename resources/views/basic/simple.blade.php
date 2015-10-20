@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
	<div class="row">
		<div class="col-md-12">
		<h1>{{ $title or NULL }}</h1><hr>

		@if ($des)
			<blockquote>
                {!! $des or NULL !!}
            </blockquote>
		@endif
		
		@if ($res)
			<div class="well">{!! $res or NULL !!}</div>
		@endif
		</div>
	</div>	
</div>
@stop