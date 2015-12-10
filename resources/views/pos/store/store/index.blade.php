@extends('base')

@section('body')

<div class="row">
	<div class="col-md-12">
		<h1>門市 
			<small><a href="{{ action('Pos\Store\StoreController@create') }}">新增</a></small>
		</h1><hr>

		<table class="table table-striped">
			<thead>
				<th>id</th>
				<th>代碼</th>
				<th>名稱</th>
				<th>地區</th>
				<th>經營中</th>
			</thead>
			<tbody>
				@foreach ($stores as $store)
				<tr>
					<td>
						<a href="{{ action('Pos\Store\StoreController@show', ['id' => $store->id]) }}">{{ $store->id }}</a>  
					</td>
					<td>{{ $store->sn }}</td>
					<td>{{ $store->name }}</td>
					{{-- <td>{{ $store->area()->name }}</td> --}}
					<td>{{ $store->is_active }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

@stop