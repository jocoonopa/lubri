@extends('base')

@section('title') 
修改商品為C贈品
@stop

@section('body')
	<div class="row">
		<div class="col-md-12">
			<h2>將勾選商品修改為C字頭贈品</h2><hr>
		</div>
		
		<div class="col-md-12">
			<h4>{{ $beforeDays }}日內新建之商品列表 </h4>
			
			@include ('common.successmsg')
			
			{!! Form::open(['method' => 'PUT', 'action' => ['Flap\PIS_Goods\FixCPrefixGoodsController@update']]) !!}	
				@include('flap.pisgoods.fixcgoods.form', ['goodses' => $goodses])
			{!! Form::close() !!}
		</div>
	</div>
@stop

@section('js')
<script>
$('#check-all').click(function () {
	$('input[name="Codes[]"]').prop('checked', true);
});

$('#inverse-check-all').click(function () {
	$('input[name="Codes[]"]').prop('checked', false);
});

$('button[type="submit"]').click(function () {
	(0 < $('input[type="checkbox"]:checked').length) ? $('form').submit() : alert('您沒有勾選任何商品編號!');

	return false;
});
</script>
@stop