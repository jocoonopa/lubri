@extends('base')

@section('title') 
修改商品為C贈品
@stop

@section('body')
	<div class="row">		
		<div class="col-md-12">
			<h4>{{ $beforeDays }}日內新建之商品列表<small><p class="text-muted">將勾選商品修改為C字頭贈品</p></small></h4><hr>
			@include ('common.successmsg')
			
			@if (0 < count($goodses))
			{!! Form::open(['method' => 'PUT', 'action' => ['Flap\PIS_Goods\FixCPrefixGoodsController@update']]) !!}	
				@include('flap.pisgoods.fixcgoods.form', ['goodses' => $goodses])
			{!! Form::close() !!}
			@else
				<div class="alert alert-info" role="alert">
					目前沒有可轉換的商品喔!
				</div>
			@endif
		</div>
	</div>
@stop

@section('js')
<script>
$('#check-all').click(function () {
	$('input[name="Codes[]"]').prop('checked', true);
	$('input[type="checkbox"]').change();
});

$('#inverse-check-all').click(function () {
	$('input[name="Codes[]"]').prop('checked', false);
	$('input[type="checkbox"]').change();
});

$('button[type="submit"]').click(function () {
	if (0 < $('input[type="checkbox"]:checked').length) {
		$(this).prop('disabled', true);
		$('form').submit();
	} else {
		bootbox.alert('您沒有勾選任何商品編號!');
	}

	return false;
});

$('input[type="checkbox"]').change(function () {
	$('button[type="submit"]').prop('disabled', (0 === $('input[type="checkbox"]:checked').length));
}).change();
</script>
@stop