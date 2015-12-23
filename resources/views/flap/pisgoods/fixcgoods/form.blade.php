<div class="form-group">
	<button id="check-all" type="button" class="btn btn-raised btn-default">全部勾選</button>
	<button id="inverse-check-all" type="button" class="btn btn-default">全部取消</button>
</div>

<div class="form-goup">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>勾選</th>
				<th>商品代碼</th>
				<th>商品名稱</th>
				<th class="hidden-xs">建立時間</th>
			</tr>
		</thead>
		<tbody>

			@foreach ($goodses as $goods)
			<tr>
				<td>
					<div class="checkbox">
						<label>
							{!! Form::checkbox('Codes[]', $goods['Code'], false) !!} 
						</label>
					</div>
				</td>
				<td>{{ $goods['Code'] }}</td>
				<td>{{ $goods['Name'] }}</td>
				<td class="hidden-xs">{{ $goods['CRT_TIME'] }}</td>
			</tr>
			@endforeach 
		</tbody>
	</table>
</div>

<div class="form-group">
	<button type="submit" class="btn btn-raised btn-primary" disabled>確認</button>
</div>