<div class="form-group">
	{!! Form::label('account', '帳號:') !!}
	{!! Form::text('account', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
	{!! Form::label('username', '姓名:') !!}
	{!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => '洪小閎']) !!}
</div>

<div class="form-group">
	{!! Form::label('ip', 'IP:') !!}
	{!! Form::text('ip', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
	{!! Form::label('ext', '分機:') !!}
	{!! Form::text('ext', null, ['class' => 'form-control', 'placeholder' => 6231]) !!}
</div>