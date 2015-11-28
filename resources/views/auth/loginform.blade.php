<div class="form-group">
	<label class="col-md-4 control-label">帳號:</label>
	<div class="col-md-6">
		<input type="text" class="form-control" name="account" value="{{ old('account') }}">
	</div>
</div>

<div class="form-group">
	<label class="col-md-4 control-label">密碼:</label>
	<div class="col-md-6">
		<input type="password" class="form-control" name="password">
	</div>
</div>

<div class="form-group">
	<div class="col-md-6 col-md-offset-4">
		<div class="checkbox">
			<label>
				<input type="checkbox" name="remember">記住我
			</label>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="col-md-6 col-md-offset-4">
		<button type="submit" class="btn btn-primary">{{ $submitButtonText }}</button>

		<a class="btn btn-link" href="{{ url('/auth/password/email') }}">忘記密碼?</a>
	</div>
</div>