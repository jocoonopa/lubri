<div class="form-group">
	<button id="check-all" type="button" class="btn btn-default">全部勾選</button>
	<button id="inverse-check-all" type="button" class="btn btn-inverse">全部取消</button>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th>勾選</th>
			<th>商品代碼</th>
			<th>商品名稱</th>
			<th>建立時間</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($goodses as $goods)
		<tr>
			<td>
				{!! Form::checkbox('Codes[]', $goods['Code'], true) !!} 
			</td>
			<td>{{ $goods['Code'] }}</td>
			<td>{{ $goods['Name'] }}</td>
			<td>{{ $goods['CRT_TIME'] }}</td>
		</tr>
		@endforeach 
	</tbody>
</table>

<div class="form-group">
	<button type="submit" class="btn btn-primary">確認</button>
</div>