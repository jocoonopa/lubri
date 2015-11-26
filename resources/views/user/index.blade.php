@extends('base')

@section('title')
使用者列表
@stop

@section('body')

<h1>使用者列表</h1>
<hr>

<table class="table table-striped">
	<thead>
		<tr>
			<td>id</td>
			<td>帳號</td>
			<td>姓名</td>
			<td>分機</td>
			<td>ip</td>
		</tr>
	</thead>
	<tbody>
		@foreach ($users as $user)
		<tr>
			<td><a href="{{ action('User\UserController@edit', ['id' => $user->id]) }}">{{ $user->id }}</a> </td>
			<td>{{ $user->account }}</td>
			<td>{{ $user->username }}</td>
			<td>{{ $user->ext }}</td>
			<td>{{ $user->ip }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@stop