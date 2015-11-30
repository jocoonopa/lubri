@extends('base')

@section('title')
輔翼口袋名單
@stop

@section('css')
<link rel="stylesheet" href="{!! URL::asset('/assets/css/mix.css') !!}">
@stop

@section('body')
<div class="row">
	<div class="col-md-12">
		<h1>輔翼口袋名單 <small><a href="{{ url('/flap/members') }}"><i class="glyphicon glyphicon-list"></i></a></small></h1><hr>
	</div>
	<div class="col-md-6">
		<h3>篩選條件:</h3>
		<button id="condition-all" class="btn btn-sm btn-primary" data-filter="all">全部</button>
		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			性別篩選 <span class="caret"></span>
			</button>
			<ul class="dropdown-menu condition">
				<li>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="sex" data-filter=".c-1" checked>男
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" name="sex" data-filter=".c-,.c-2" checked>女
						</label>
					</div>
				</li>
			</ul>
		</div>

		<div class="btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			年齡層篩選 <span class="caret"></span>
			</button>
			<ul class="dropdown-menu condition">
				<li>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="YG" data-filter=".YG-1" checked>20歲以下
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" name="YG" data-filter=".YG-2" checked>21 ~ 35
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" name="YG" data-filter=".YG-3" checked>36 ~ 50
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" name="YG" data-filter=".YG-4" checked>51 ~ 65
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" name="YG" data-filter=".YG-5" checked>66 ~ 80
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" name="YG" data-filter=".YG-6" checked>81歲以上
						</label>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="col-md-6">
		<h3>排序條件:</h3>
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			年齡 <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li><a href="#" class="sort" data-sort="age:asc">由小到老<i class="glyphicon glyphicon-sort-by-order"></i></a></li>
				<li><a href="#" class="sort" data-sort="age:desc">由老到小<i class="glyphicon glyphicon-sort-by-order-alt"></i></a></li>
			</ul>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			累計消費 <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li><a href="#" class="sort" data-sort="totalconsume:asc">小戶到大戶<i class="glyphicon glyphicon-sort-by-order"></i></a></li>
				<li><a href="#" class="sort" data-sort="totalconsume:desc">大戶到小戶<i class="glyphicon glyphicon-sort-by-order-alt"></i></a></li>
			</ul>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			現有紅利 <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li><a href="#" class="sort" data-sort="totalbonus:asc">清寒到土豪<i class="glyphicon glyphicon-sort-by-order"></i></a></li>
				<li><a href="#" class="sort" data-sort="totalbonus:desc">土豪到清寒<i class="glyphicon glyphicon-sort-by-order-alt"></i></a></li>
			</ul>
		</div>
	</div>

	<div class="col-md-12"><hr></div>
	
	<div id="Container">
		@foreach ($members as $member)
			@include ('flap.members.panelcontent.info', ['member', $member])
		@endforeach
	</div>
</div>
@stop

@section('js')
<script src="http://cdn.jsdelivr.net/jquery.mixitup/latest/jquery.mixitup.min.js"></script>
<script>
function genMixFilterValue() {
	var $condition = $('.condition');
	var arr = [];
	var tmp = [];

	$condition.each(function () {
		var isFirst = (0 === arr.length);
		var $check = $(this).find('input[type="checkbox"]:checked');

		$check.each(function () {
			var $this = $(this);

			if (isFirst) {
				arr.push($this.data('filter'));
			} else {
				arr.forEach(function (entry) {
					tmp.push(entry + ($this.data('filter')));
				});					
			}
		});

		if (0 < tmp.length) {
			arr = tmp;
			tmp = [];
		}
	});

	return arr.join();
}

$('#Container').mixItUp();
$('[data-toggle="popover"]').popover();
$('input[type="checkbox"]').change(function () {
	var targetClassName = genMixFilterValue();

	$('#Container').mixItUp('multiMix', {filter: targetClassName});
});

$('#condition-all').click(function () {
	$('input[type="checkbox"]').each(function () {
		$(this).prop('checked', true);
	});

	$('input[type="checkbox"]').first().change();
});
</script>
@stop