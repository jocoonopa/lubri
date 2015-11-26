<div class="form-group">
	{!! Form::label('password', '密碼:') !!}
	{!! Form::password('password', ['class' => 'form-control', 'placeholder' => '長度需大於八個字母，請至少包含一個以上的英文以及數字']) !!}
</div>

<div class="form-group">
	{!! Form::label('password', '確認密碼:') !!}
	{!! Form::password('password', ['class' => 'form-control', 'placeholder' => '重複輸入密碼']) !!}
</div>