<div class="mix col-md-4 {{assignAgeGroup($member['cust_birthday'])}} c-{{$member['cust_sex']}}"
data-age="{{tranAge($member['cust_birthday'])}}" data-totalconsume="{{$member['cust_totalconsume']}}" data-totalbonus="{{$member['cust_bonus']}}"
>
	<div class="panel panel-default">
		<div class="panel-heading" data-toggle="popover" data-placement="top" data-html="true" data-content="{{$member['ob_memo'] .'<br/>'. $member['cust_memo'] .'<br/>'. $member['fn_memo'] }}">
			<h4>
				<a href="{{url('flap/members/' . $member['cust_id'])}}">{{$member['cust_cname'] . tranSex($member['cust_sex'])}}</a> <small>
				@if ($member['cust_birthday'] !== '') 
					{{tranAge($member['cust_birthday'])}}歲
				@else 未知 @endif
				</small>

				@if ($member['ob_memo'] !== '' || $member['cust_memo'] !== '' || $member['fn_memo'] !== '' ) <span class="badge pull-right"><i class="glyphicon glyphicon-comment"></i></span>@endif
			</h4>	 
		</div>
		<div class="panel-body">
			<div class="list-group">
				<span class="list-group-item"><b>客代:&nbsp;</b>{{$member['cust_id']}}</span>
				<span class="list-group-item"><b>手機:&nbsp;</b>{{$member['cust_mobilphone']}}</span>
				<span class="list-group-item"><b>信箱:&nbsp;</b>{{$member['cust_email']}}</span>
				<span class="list-group-item"><b>生日:&nbsp;</b>{{$member['cust_birthday']}}</span>
				<span class="list-group-item"><b>住家電話:&nbsp;</b>{{$member['cust_tel1']}}</span>
				<span class="list-group-item"><b>公司電話:&nbsp;</b>{{$member['cust_tel2']}}</span>
				<span class="list-group-item"><b>現有紅利:&nbsp;</b>{{number_format((int) $member['cust_bonus'])}}</span>
				<span class="list-group-item"><b>累計消費金額:&nbsp;</b>{{number_format((int) $member['cust_totalconsume'])}}</span>
				<span class="list-group-item"><b>首次消費日:&nbsp;</b> {{ substr($member['ob_firstbuy'], 0, 10)}}</span>
				<span class="list-group-item"><b>最後消費日:&nbsp;</b>{{ substr($member['Cust_traxdate'], 0, 10)}}</span>
			</div>
		</div>
	</div>
</div>