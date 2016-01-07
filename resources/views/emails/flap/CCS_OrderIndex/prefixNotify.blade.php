<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="UTF-8">
	<title>康萃特單號前綴修改_{{ date('Y-m-d H:i:s')}}</title>
</head>
<body>
	<div>
		<table>
			<thead>
				<tr>
					<th>原單號</th>
					<th>修改後單號</th>
					<th>修改後子單號</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($modifyOrders as $order)
				<tr>
					<td>{{ $order->getOriginFirstName() }}</td>
					<td>{{ $order->getFirstName() }}</td>
					<td>	
						<ul>
							@foreach ($order->getChildren() as $child)	
								<li>{{ $child }}</li>		
							@endforeach
						</ul>
					</td>
				</tr>
				@endforeach
			</tbody>	
		</table>
		
		<hr>
		<p>請知悉，謝謝!</p>
	</div>
</body>
</html>