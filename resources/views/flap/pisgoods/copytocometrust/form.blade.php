<div class="form-group">
	<button id="check-all" type="button" class="btn btn-raised btn-default">全部勾選</button>
	<button id="inverse-check-all" type="button" class="btn btn-default">全部取消</button>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th>勾選</th>
			<th>商品代碼</th>
			<th>對應康萃特代碼</th>
			<th class="hidden-xs">商品名稱</th>
			<th class="hidden-xs">建立時間</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@foreach ($goodses as $goods)
		<tr data-code="{{ $goods['Code'] }}">
			<td class="form-group">
				@if (false === $goods['ctCode']) 
					<div class="checkbox">
						<label>
							{!! Form::checkbox('Codes[]', $goods['Code'], false) !!} 
						</label>
					</div>
				@endif
			</td>
			<td>{{ $goods['Code'] }}</td>
			<td>
				@if (false !== $goods['ctCode']) 
				{{ $goods['ctCode'] }}
				@endif
			</td>
			<td class="hidden-xs">{{ $goods['Name'] }}</td>
			<td class="hidden-xs">{{ $goods['CRT_TIME'] }}</td>
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
	<button type="submit" class="btn btn-raised btn-primary" disabled>確認</button>
</div>