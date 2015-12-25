@extends('base')

@section('title')
2016行銷KPI報表待辦清單
@stop

@section('body')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<h4>2016行銷KPI報表待辦清單</h4><hr>
			
			<div class="list-group">
				@foreach ($storys as $story)
					<div class="list-group-item">
						<div class="row-action-primary">
							<i class="material-icons">{{ $story['icon'] }}</i>
						</div>
						<div class="row-content">
							<div class="least-content">{{ $story['index']}} <span class="badge">{{ $story['heri'] }}</span> </div>
							<h4 class="list-group-item-heading"><span class="label label-info">{{ $story['point'] }}</span></h4>
							<p class="list-group-item-text">{{ $story['content'] }}</p>
						</div>
					</div>
					<div class="list-group-separator"></div>
			  	@endforeach
			</div>
		</div>
	</div>
</div>
@endsection