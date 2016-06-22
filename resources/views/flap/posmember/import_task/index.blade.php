@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
	<div class="row">
		<div class="col-md-12">
			<h1>{{$title}} <small><a href="/flap/pos_member/import_task/create?kind_id={{Input::get('kind_id')}}" class="btn btn-raised btn-sm btn-primary">
			<i class="glyphicon glyphicon-plus"></i>
			新增任務</a></small>

			<small><a href="/flap/pos_member/import_kind" class="pull-right btn btn-raised btn-sm btn-default">
			<i class="glyphicon glyphicon-arrow-left"></i>
			回到匯入選擇
			</a></small></h1>

			@include('common.successmsg')
			@include('common.errormsg')

			<table class="table">
				<thead>
					<tr>
						<th>任務</th>
						<th>匯入費時</th>
						<th>推送費時</th>
						<th>筆數</th>
						<!-- <th>已推送</th> -->
						<th>建立時間</th>
						<!-- <th>推送完成時間</th> -->
						<th>建立者</th>
						<th>狀態</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					@each('flap.posmember.import_task._tbody', $tasks, 'task')
				</tbody>					
			</table>
		</div>
	</div>	
</div>
@stop

@section('js')
<script src="/assets/js/jquery.blockui.js"></script>
<script src="/assets/js/facade.js"></script>
<script src="/assets/js/importtask.js"></script>
@stop

