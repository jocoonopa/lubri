@if (count($errors) > 0)
<div class="alert alert-danger">
	<ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="form-group">
	<label class="col-md-4 control-label">信箱:</label>
	<div class="col-md-6">
		<input type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="example@chinghwa.com.tw">
	</div>
</div>

<div class="form-group">
	<div class="col-md-6 col-md-offset-4">
		<button type="submit" class="btn btn-primary" @if(Session::has('success')) disabled @endif>{{ $submitButtonText }}</button>
	</div>
</div>

