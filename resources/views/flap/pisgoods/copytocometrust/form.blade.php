<div class="form-group">
	<button id="check-all" type="button" class="btn btn-default">全部勾選</button>
	<button id="inverse-check-all" type="button" class="btn btn-inverse">全部取消</button>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th>勾選</th>
			<th>商品代碼</th>
			<th>對應康萃特代碼</th>
			<th>商品名稱</th>
			<th>建立時間</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@foreach ($goodses as $goods)
		<tr data-code="{{ $goods['Code'] }}">
			<td>
				@if (false === $goods['ctCode']) 
					{!! Form::checkbox('Codes[]', $goods['Code'], false) !!} 
				@endif
			</td>
			<td>{{ $goods['Code'] }}</td>
			<td>
				@if (false !== $goods['ctCode']) 
					{{ $goods['ctCode'] }}
				@endif
			</td>
			<td>{{ $goods['Name'] }}</td>
			<td>{{ $goods['CRT_TIME'] }}</td>
			<td>
				<button class="jq-remove btn btn-warning btn-xs">
					<span class="glyphicon glyphicon-remove"></span>
				</button>
			</td>
		</tr>
		@endforeach 
	</tbody>
</table>

<div class="form-group">
	<button type="submit" class="btn btn-primary" disabled>確認</button>
</div>