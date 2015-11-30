@extends('base')

@section('title')
會員詳細資料頁
@stop

@section('body')
	<div class="row">
		<div class="col-md-12">
			<dl>
			  <dt>會員編號:</dt>
			  <dd>{{$code}}</dd>
			</dl>
		</div>
	</div>
@stop