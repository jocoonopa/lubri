@extends('base')

@section('title')
輔翼口袋名單
@stop

@section('css')

@stop

@section('body')
<div class="row">
	<div class="col-md-12">
		<h1>輔翼口袋名單 <small><a href="{{ url('/flap/members?type=mix') }}"><i class="glyphicon glyphicon-th"></i></a></small></h1><hr>

		<table class="table table-striped">
			<thead>
				<th>客代</th>
				<th>姓名</th>
				<th>性別</th>		
				<th>最後購物日</th>
				<th>累計訂購金額</th>
				<th>現有紅利</th>
				<th>開始經營日</th>
				<th>生日</th>
				<th>手機</th>
				<th>住家電話</th>
				<th>公司電話</th>
				<th>備註</th>
			</thead>
			<tbody>
			@foreach ($members as $member) 
				<tr>
					<td>{{$member['cust_id']}}</td>
					<td>{{$member['cust_cname']}}</td>
					<td>{{ tranSex($member['cust_sex']) }}</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>{{$member['cust_birthday']}}</td>			
					<td>{{$member['cust_mobilphone']}}</td>
					<td>{{$member['cust_tel1']}}</td>
					<td>{{$member['cust_tel2']}}</td>
					<td></td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop

@section('js')
<script src="{!! URL::asset('/assets/js/jquery.tablesorter.min.js') !!}"></script>
<script src="{!! URL::asset('/assets/js/jquery.tablesorter.widgets.min.js') !!}"></script>
<script>
(function () {
	$('table').tablesorter({
		widgets        : ['zebra', 'columns'],
		usNumberFormat : false,
		sortReset      : true,
		sortRestart    : true
	});
})();	
</script>
@stop